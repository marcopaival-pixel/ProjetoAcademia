<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiV1ContractTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Rotas documentadas no OpenAPI (paths relativos a /api/v1).
     *
     * @return array<string, list<string>>
     */
    private function documentedRoutes(): array
    {
        $yaml = file_get_contents(base_path('docs/openapi-v1.yaml'));
        $routes = [];

        if (preg_match_all('/^  (\/[^\s:]+):\s*$/m', $yaml, $pathMatches)) {
            foreach ($pathMatches[1] as $path) {
                $routes[$this->normalizeOpenApiPath($path)] = [];
            }
        }

        $offset = 0;
        while (preg_match('/^  (\/[^\s:]+):\s*$/m', $yaml, $pathMatch, PREG_OFFSET_CAPTURE, $offset)) {
            $path = $this->normalizeOpenApiPath($pathMatch[1][0]);
            $start = $pathMatch[0][1];
            $next = strpos($yaml, "\n  /", $start + 1);
            $block = $next === false ? substr($yaml, $start) : substr($yaml, $start, $next - $start);

            if (preg_match_all('/^\s{4}(get|post|put|patch|delete):/m', $block, $methodMatches)) {
                $routes[$path] = array_map('strtoupper', $methodMatches[1]);
            }

            $offset = $start + strlen($pathMatch[0][0]);
        }

        return $routes;
    }

    /**
     * @return array<string, list<string>>
     */
    private function registeredRoutes(): array
    {
        $routes = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (! str_starts_with($uri, 'api/v1/') && $uri !== 'api/v1/health' && ! str_starts_with($uri, 'api/v1')) {
                continue;
            }

            if ($uri === 'api/v1/health' || str_starts_with($uri, 'api/v1/')) {
                $relative = '/'.ltrim(substr($uri, strlen('api/v1')), '/');
                if ($relative === '/') {
                    $relative = '/health';
                }

                $relative = preg_replace('/\{[^}]+\}/', '{id}', $relative) ?? $relative;
                $relative = str_replace(['{training_plan}', '{foodEntry}', '{assessment}', '{photo}', '{jobKey}', '{type}'], ['{id}', '{id}', '{id}', '{id}', '{id}', '{id}'], $relative);

                foreach ($route->methods() as $method) {
                    if (in_array($method, ['HEAD', 'OPTIONS'], true)) {
                        continue;
                    }
                    $routes[$relative][] = $method;
                }
            }
        }

        foreach ($routes as $path => $methods) {
            $routes[$path] = array_values(array_unique($methods));
        }

        return $routes;
    }

    public function test_openapi_documents_all_api_v1_routes(): void
    {
        $documented = $this->documentedRoutes();
        $registered = $this->registeredRoutes();

        $missingInOpenApi = [];
        foreach ($registered as $path => $methods) {
            if ($path === '/health' && isset($documented['/health'])) {
                continue;
            }

            if (! isset($documented[$path])) {
                $missingInOpenApi[] = $path.' ('.implode(',', $methods).')';
                continue;
            }

            foreach ($methods as $method) {
                if (! in_array($method, $documented[$path], true)) {
                    $missingInOpenApi[] = "{$method} {$path}";
                }
            }
        }

        $this->assertSame([], $missingInOpenApi, 'Rotas API sem documentação OpenAPI: '.implode('; ', $missingInOpenApi));
    }

    public function test_openapi_has_no_orphan_paths(): void
    {
        $documented = array_keys($this->documentedRoutes());
        $registered = array_keys($this->registeredRoutes());

        $orphans = array_diff($documented, $registered);

        $this->assertSame([], array_values($orphans), 'Paths OpenAPI sem rota Laravel correspondente.');
    }

    private function normalizeOpenApiPath(string $path): string
    {
        return str_replace(
            ['{jobKey}', '{type}', '{id}'],
            ['{id}', '{id}', '{id}'],
            $path
        );
    }
}
