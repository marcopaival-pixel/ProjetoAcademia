<?php

namespace App\Support;

use InvalidArgumentException;

class AppVersion
{
    private static ?string $rootOverride = null;

    /** Apenas testes — não usar em produção. */
    public static function useRootForTesting(?string $absolutePath): void
    {
        self::$rootOverride = $absolutePath;
    }

    public static function projectRoot(): string
    {
        return self::$rootOverride ?? base_path();
    }

    public static function versionFilePath(): string
    {
        return self::projectRoot() . DIRECTORY_SEPARATOR . 'VERSION';
    }

    public static function current(): string
    {
        if (function_exists('app') && app()->bound('config')) {
            $fromConfig = config('app.version');
            if (is_string($fromConfig) && $fromConfig !== '') {
                return self::normalize($fromConfig);
            }
        }

        $path = self::versionFilePath();
        if (is_file($path)) {
            $raw = trim((string) file_get_contents($path));
            if ($raw !== '') {
                return self::normalize($raw);
            }
        }

        return '0.0.0';
    }

    public static function display(): string
    {
        return 'v' . self::current();
    }

    public static function write(string $version): void
    {
        $normalized = self::normalize($version);
        file_put_contents(self::versionFilePath(), $normalized . PHP_EOL);
    }

    public static function bump(string $part): string
    {
        $parts = self::parse(self::current());

        match ($part) {
            'patch' => $parts[2]++,
            'minor' => [$parts[1]++, $parts[2] = 0],
            'major' => [$parts[0]++, $parts[1] = 0, $parts[2] = 0],
            default => throw new InvalidArgumentException("Bump inválido: {$part}"),
        };

        $next = implode('.', $parts);
        self::write($next);

        return $next;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    public static function parse(string $version): array
    {
        $normalized = self::normalize($version);
        $core = preg_replace('/^(\d+\.\d+\.\d+).*/', '$1', $normalized) ?? $normalized;

        if (! preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $core, $m)) {
            throw new InvalidArgumentException("Versão semver inválida: {$version}");
        }

        return [(int) $m[1], (int) $m[2], (int) $m[3]];
    }

    public static function normalize(string $version): string
    {
        return ltrim(trim($version), 'vV');
    }

    public static function appendChangelogSection(string $version, string $note = ''): void
    {
        $path = self::projectRoot() . DIRECTORY_SEPARATOR . 'CHANGELOG.md';
        $date = now()->format('Y-m-d');
        $block = "## [{$version}] - {$date}\n\n";
        $block .= "### Alterações\n";
        $block .= $note !== '' ? "- {$note}\n" : "- Release registrada.\n";
        $block .= "\n";

        if (! is_file($path)) {
            file_put_contents($path, "# Changelog\n\n{$block}");

            return;
        }

        $contents = file_get_contents($path);
        $marker = "## [Unreleased]";

        if (is_string($contents) && str_contains($contents, $marker)) {
            $replacement = $block . $marker;
            $contents = preg_replace('/## \[Unreleased\][^\n]*\n/', $replacement, $contents, 1) ?? ($block . $contents);
        } else {
            $contents = "# Changelog\n\n{$block}" . $contents;
        }

        file_put_contents($path, $contents);
    }
}
