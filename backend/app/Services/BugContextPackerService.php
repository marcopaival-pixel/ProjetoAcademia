<?php

namespace App\Services;

use App\Models\BugIncident;
use App\Models\SystemError;
use Illuminate\Support\Str;

/**
 * Monta pacote mínimo de arquivos para análise do agente (stack → grafo leve → snippets).
 */
class BugContextPackerService
{
    private const MAX_FILES = 12;

    private const SNIPPET_RADIUS = 45;

    public function __construct(
        private readonly BugLogMaskingService $masking,
    ) {}

    /** @return array<string, mixed> */
    public function build(SystemError $error, BugIncident $incident): array
    {
        $stack = (string) $error->stack_trace;
        $frames = $this->parseAppFrames($stack);
        $primary = $frames[0] ?? null;

        $related = [];
        if ($primary !== null) {
            $related = $this->collectRelatedFiles($primary, $frames, $error);
        }

        $routeHint = $this->resolveRouteHint($error->method, $error->url);
        $gitSuspects = $this->gitRecentChanges(
            array_map(fn (array $f) => $f['repo_path'], $related),
            72
        );

        return [
            'version' => 1,
            'incident_code' => $incident->incident_code,
            'generated_at' => now()->toIso8601String(),
            'primary_frame' => $primary,
            'stack_frames_app' => array_slice($frames, 0, 8),
            'related_files' => $related,
            'route_hint' => $routeHint,
            'git_suspects' => $gitSuspects,
            'stats' => [
                'files_in_pack' => count($related),
                'max_files' => self::MAX_FILES,
                'snippet_radius_lines' => self::SNIPPET_RADIUS,
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function parseAppFrames(string $stack): array
    {
        $frames = [];
        $patterns = [
            '/(?:at\s+)?((?:app|database|routes|config)[\/\\\\][A-Za-z0-9_\/\\\\.-]+\.php)[:(](\d+)\)?/i',
            '/((?:App\\\\)[A-Za-z0-9\\\\]+)\((\d+)\)/',
            '/([A-Za-z0-9_]+Controller\.php)[:(](\d+)\)?/',
            '/([A-Za-z0-9_]+Service\.php)[:(](\d+)\)?/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $stack, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $frame = $this->normalizeFrame($m[1], (int) $m[2], $stack);
                    if ($frame !== null) {
                        $frames[$frame['repo_path']] = $frame;
                    }
                }
            }
        }

        return array_values($frames);
    }

    /** @return array<string, mixed>|null */
    private function normalizeFrame(string $rawPath, int $line, string $stack): ?array
    {
        $rawPath = str_replace('\\', '/', $rawPath);

        if (str_contains($rawPath, 'vendor/') || str_contains($rawPath, 'Vendor/')) {
            return null;
        }

        if (str_starts_with($rawPath, 'App/') || str_starts_with($rawPath, 'App\\')) {
            $class = str_replace('/', '\\', $rawPath);
            $repoPath = $this->classToRepoPath($class);
        } elseif (str_contains($rawPath, '/')) {
            $repoPath = 'backend/'.ltrim($rawPath, '/');
        } else {
            $repoPath = $this->findFileByBasename($rawPath);
            if ($repoPath === null) {
                return null;
            }
        }

        $abs = $this->absolutePath($repoPath);
        if ($abs === null || ! is_file($abs)) {
            return null;
        }

        $method = $this->guessMethodAtLine($abs, $line) ?? $this->guessMethodFromStack($stack, basename($abs));

        return [
            'repo_path' => $repoPath,
            'line' => $line,
            'class' => $this->classFromRepoPath($repoPath),
            'method' => $method,
            'role' => $this->inferRole($repoPath),
        ];
    }

    /**
     * @param  array<string, mixed>  $primary
     * @param  list<array<string, mixed>>  $frames
     * @return list<array<string, mixed>>
     */
    private function collectRelatedFiles(array $primary, array $frames, SystemError $error): array
    {
        $seen = [];
        $pack = [];

        $add = function (array $meta) use (&$pack, &$seen): void {
            if (count($pack) >= self::MAX_FILES) {
                return;
            }
            $path = $meta['repo_path'];
            if (isset($seen[$path])) {
                return;
            }
            $abs = $this->absolutePath($path);
            if ($abs === null || ! is_file($abs)) {
                return;
            }
            $seen[$path] = true;
            $line = $meta['line'] ?? null;
            $snippet = $this->readSnippet($abs, $line);
            $pack[] = [
                'role' => $meta['role'] ?? $this->inferRole($path),
                'repo_path' => $path,
                'reason' => $meta['reason'] ?? 'related',
                'class' => $meta['class'] ?? $this->classFromRepoPath($path),
                'method' => $meta['method'] ?? null,
                'line' => $line,
                'line_start' => $snippet['line_start'],
                'line_end' => $snippet['line_end'],
                'snippet' => $this->masking->mask($snippet['content']),
            ];
        };

        $add([
            'repo_path' => $primary['repo_path'],
            'line' => $primary['line'],
            'role' => $primary['role'],
            'class' => $primary['class'],
            'method' => $primary['method'],
            'reason' => 'stack_primary',
        ]);

        foreach ($frames as $frame) {
            $add(array_merge($frame, ['reason' => 'stack_frame']));
        }

        $primaryAbs = $this->absolutePath($primary['repo_path']);
        if ($primaryAbs !== null) {
            foreach ($this->importsFromFile($primaryAbs) as $import) {
                $repoPath = $this->classToRepoPath($import);
                $add([
                    'repo_path' => $repoPath,
                    'role' => $this->inferRole($repoPath),
                    'class' => $import,
                    'reason' => 'import_primary',
                ]);

                if ($this->inferRole($repoPath) === 'model') {
                    foreach ($this->migrationsForModel($import) as $migration) {
                        $add([
                            'repo_path' => $migration,
                            'role' => 'migration',
                            'reason' => 'model_table',
                        ]);
                    }
                }
            }

            foreach ($this->findCallers($primary['class'] ?? '', $primary['method'] ?? '') as $caller) {
                $add(array_merge($caller, ['reason' => 'calls_primary']));
            }
        }

        if ($routeHint = $this->resolveRouteHint($error->method, $error->url)) {
            if (! empty($routeHint['controller_repo_path'])) {
                $add([
                    'repo_path' => $routeHint['controller_repo_path'],
                    'role' => 'controller',
                    'class' => $routeHint['controller_class'] ?? null,
                    'method' => $routeHint['controller_method'] ?? null,
                    'reason' => 'route_match',
                ]);
            }
        }

        return $pack;
    }

    /** @return array{line_start: int, line_end: int, content: string} */
    private function readSnippet(string $absolutePath, ?int $focusLine): array
    {
        $lines = file($absolutePath, FILE_IGNORE_NEW_LINES) ?: [];
        $total = count($lines);
        if ($total === 0) {
            return ['line_start' => 1, 'line_end' => 1, 'content' => ''];
        }

        $focus = max(1, min($focusLine ?? 1, $total));
        $start = max(1, $focus - self::SNIPPET_RADIUS);
        $end = min($total, $focus + self::SNIPPET_RADIUS);
        $chunk = array_slice($lines, $start - 1, $end - $start + 1);

        return [
            'line_start' => $start,
            'line_end' => $end,
            'content' => implode("\n", $chunk),
        ];
    }

    /** @return list<string> */
    private function importsFromFile(string $absolutePath): array
    {
        $content = file_get_contents($absolutePath);
        if ($content === false) {
            return [];
        }

        preg_match_all('/^use\s+(App\\\\[^;]+);/m', $content, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    /** @return list<string> */
    private function migrationsForModel(string $modelClass): array
    {
        $base = class_basename(str_replace('/', '\\', $modelClass));
        $table = Str::snake(Str::pluralStudly($base));

        $hits = [];
        foreach (glob(base_path('database/migrations/*.php')) ?: [] as $file) {
            $name = basename($file);
            if (str_contains($name, $table)) {
                $hits[] = 'backend/database/migrations/'.$name;
            }
        }

        return array_slice($hits, 0, 2);
    }

    /** @return list<array<string, mixed>> */
    private function findCallers(string $class, ?string $method): array
    {
        if ($class === '' || $method === null || $method === '') {
            return [];
        }

        $short = class_basename($class);
        $needle = '->'.$method.'(';
        $staticNeedle = '::'.$method.'(';
        $hits = [];

        foreach ([base_path('app/Http'), base_path('app/Services'), base_path('routes')] as $dir) {
            if (! is_dir($dir)) {
                continue;
            }
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }
                $content = file_get_contents($file->getPathname());
                if ($content === false) {
                    continue;
                }
                if (! str_contains($content, $needle) && ! str_contains($content, $staticNeedle) && ! str_contains($content, $short)) {
                    continue;
                }
                if (! str_contains($content, $needle) && ! str_contains($content, $staticNeedle)) {
                    continue;
                }
                $repoPath = 'backend/'.str_replace('\\', '/', Str::after($file->getPathname(), base_path().DIRECTORY_SEPARATOR));
                $hits[] = [
                    'repo_path' => $repoPath,
                    'role' => $this->inferRole($repoPath),
                    'class' => $this->classFromRepoPath($repoPath),
                    'method' => $method,
                ];
                if (count($hits) >= 3) {
                    return $hits;
                }
            }
        }

        return $hits;
    }

    /** @return array<string, mixed>|null */
    private function resolveRouteHint(?string $method, ?string $url): ?array
    {
        $path = parse_url((string) $url, PHP_URL_PATH) ?: (string) $url;
        if ($path === '') {
            return null;
        }

        $method = strtoupper($method ?? 'GET');
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        $lastSegments = array_slice($segments, -3);

        foreach ([base_path('routes/api.php'), base_path('routes/web.php'), base_path('routes/features.php'), base_path('routes/admin.php')] as $routeFile) {
            if (! is_file($routeFile)) {
                continue;
            }
            $content = file_get_contents($routeFile);
            if ($content === false) {
                continue;
            }

            foreach ($lastSegments as $segment) {
                if (strlen($segment) < 3) {
                    continue;
                }
                if (! str_contains($content, $segment)) {
                    continue;
                }

                if (preg_match('/Route::(?:get|post|put|patch|delete|match)\([^\'"]*'.$segment.'[^\'"]*[\'"],\s*\[([^:]+)::class,\s*[\'"](\w+)[\'"]\]/i', $content, $m)) {
                    $controllerClass = trim($m[1]);
                    if (! str_starts_with($controllerClass, 'App\\')) {
                        $controllerClass = 'App\\Http\\Controllers\\'.ltrim($controllerClass, '\\');
                    }
                    $repoPath = $this->classToRepoPath($controllerClass);

                    return [
                        'http_method' => $method,
                        'path' => $path,
                        'matched_segment' => $segment,
                        'route_file' => 'backend/routes/'.basename($routeFile),
                        'controller_class' => $controllerClass,
                        'controller_method' => $m[2],
                        'controller_repo_path' => $repoPath,
                    ];
                }
            }
        }

        return [
            'http_method' => $method,
            'path' => $path,
            'matched_segment' => null,
            'route_file' => null,
            'controller_class' => null,
            'controller_method' => null,
            'controller_repo_path' => null,
        ];
    }

    /** @param  list<string>  $repoPaths
     * @return list<array<string, mixed>>
     */
    private function gitRecentChanges(array $repoPaths, int $hours): array
    {
        $repoRoot = is_dir(base_path('../.git')) ? base_path('..') : base_path();
        if (! is_dir($repoRoot.'/.git')) {
            return [];
        }

        $since = now()->subHours($hours)->format('Y-m-d H:i:s');
        $suspects = [];

        foreach (array_unique($repoPaths) as $repoPath) {
            $rel = str_starts_with($repoPath, 'backend/')
                ? substr($repoPath, strlen('backend/'))
                : $repoPath;
            $nullRedirect = PHP_OS_FAMILY === 'Windows' ? '2>NUL' : '2>/dev/null';
            $cmd = sprintf(
                'git -C %s log --since=%s --name-only --pretty=format: -- %s %s',
                escapeshellarg($repoRoot),
                escapeshellarg($since),
                escapeshellarg($rel),
                $nullRedirect
            );
            $output = shell_exec($cmd) ?? '';
            $commits = array_filter(array_map('trim', explode("\n", $output)));
            if ($commits !== []) {
                $suspects[] = [
                    'repo_path' => $repoPath,
                    'changes_last_'.$hours.'h' => count(array_unique($commits)),
                ];
            }
        }

        usort($suspects, fn ($a, $b) => ($b['changes_last_'.$hours.'h'] ?? 0) <=> ($a['changes_last_'.$hours.'h'] ?? 0));

        return array_slice($suspects, 0, 5);
    }

    private function classToRepoPath(string $class): string
    {
        $class = ltrim(str_replace('/', '\\', $class), '\\');
        if (! str_starts_with($class, 'App\\')) {
            $class = 'App\\'.$class;
        }

        return 'backend/'.str_replace('\\', '/', preg_replace('/^App\\\\/', 'app/', $class)).'.php';
    }

    private function classFromRepoPath(string $repoPath): ?string
    {
        if (! str_contains($repoPath, 'app/')) {
            return null;
        }

        $relative = Str::after($repoPath, 'app/');
        $relative = preg_replace('/\.php$/', '', $relative) ?? $relative;

        return 'App\\'.str_replace('/', '\\', $relative);
    }

    private function inferRole(string $repoPath): string
    {
        if (str_contains($repoPath, '/Http/Controllers/')) {
            return 'controller';
        }
        if (str_contains($repoPath, '/Services/')) {
            return 'service';
        }
        if (str_contains($repoPath, '/Models/')) {
            return 'model';
        }
        if (str_contains($repoPath, '/Http/Requests/')) {
            return 'request';
        }
        if (str_contains($repoPath, '/Policies/')) {
            return 'policy';
        }
        if (str_contains($repoPath, 'database/migrations/')) {
            return 'migration';
        }
        if (str_contains($repoPath, 'routes/')) {
            return 'route';
        }

        return 'code';
    }

    private function absolutePath(string $repoPath): ?string
    {
        $normalized = str_replace('\\', '/', $repoPath);
        if (str_starts_with($normalized, 'backend/')) {
            return base_path(substr($normalized, strlen('backend/')));
        }

        return base_path($normalized);
    }

    private function findFileByBasename(string $basename): ?string
    {
        $appRoot = base_path('app');
        if (! is_dir($appRoot)) {
            return null;
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($appRoot));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $basename) {
                $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($appRoot) + 1));

                return 'backend/app/'.$relative;
            }
        }

        return null;
    }

    private function guessMethodAtLine(string $absolutePath, int $line): ?string
    {
        $lines = file($absolutePath, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return null;
        }

        for ($i = min($line, count($lines)) - 1; $i >= max(0, $line - 80); $i--) {
            if (preg_match('/function\s+(\w+)\s*\(/', $lines[$i], $m)) {
                return $m[1];
            }
        }

        return null;
    }

    private function guessMethodFromStack(string $stack, string $basename): ?string
    {
        if (preg_match('/'.preg_quote($basename, '/').'.*?->(\w+)\(/', $stack, $m)) {
            return $m[1];
        }

        return null;
    }
}
