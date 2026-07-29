<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ApiSmokeTestCommand extends Command
{
    protected $signature = 'app:api:smoke
                            {--url= : Base URL (padrão APP_URL)}
                            {--email= : E-mail para login (ou SMOKE_TEST_EMAIL)}
                            {--password= : Senha (ou SMOKE_TEST_PASSWORD)}
                            {--context= : ID do vínculo paciente (ou usa o primeiro de /patient/links)}';

    protected $description = 'Smoke test da API v1 para homologação/produção (health, auth, paciente).';

    public function handle(): int
    {
        $baseUrl = rtrim((string) ($this->option('url') ?: config('app.url')), '/');
        $failed = 0;

        $this->info("=== Smoke test API — {$baseUrl} ===");
        $this->newLine();

        $failed += $this->check('GET /api/v1/health', function () use ($baseUrl) {
            $response = Http::timeout(15)->acceptJson()->get("{$baseUrl}/api/v1/health");

            if (! $response->successful()) {
                return "HTTP {$response->status()}";
            }

            if (($response->json('status') ?? null) !== 'ok') {
                return 'Resposta sem status=ok';
            }

            return null;
        });

        $failed += $this->check('GET /up (Laravel)', function () use ($baseUrl) {
            $response = Http::timeout(15)->get("{$baseUrl}/up");

            if (! $response->successful()) {
                return "HTTP {$response->status()}";
            }

            return null;
        });

        $email = (string) ($this->option('email') ?: env('SMOKE_TEST_EMAIL', ''));
        $password = (string) ($this->option('password') ?: env('SMOKE_TEST_PASSWORD', ''));

        if ($email === '' || $password === '') {
            $this->warn('  [skip] Auth/paciente — defina --email/--password ou SMOKE_TEST_EMAIL/SMOKE_TEST_PASSWORD');
            $this->newLine();

            return $failed > 0 ? self::FAILURE : self::SUCCESS;
        }

        $token = null;
        $failed += $this->check('POST /api/v1/auth/token', function () use ($baseUrl, $email, $password, &$token) {
            $response = Http::timeout(15)->acceptJson()->post("{$baseUrl}/api/v1/auth/token", [
                'email' => $email,
                'password' => $password,
                'device_name' => 'smoke-test',
            ]);

            if (! $response->successful()) {
                return "HTTP {$response->status()}: ".($response->json('message') ?? $response->body());
            }

            $token = $response->json('access_token');
            if (! is_string($token) || $token === '') {
                return 'Resposta sem access_token';
            }

            if (blank($response->json('expires_at'))) {
                return 'Resposta sem expires_at';
            }

            return null;
        });

        if (! is_string($token) || $token === '') {
            $this->error("Smoke test: {$failed} falha(s).");

            return self::FAILURE;
        }

        $auth = ['Authorization' => "Bearer {$token}"];

        $failed += $this->check('POST /api/v1/auth/token/refresh', function () use ($baseUrl, $auth, &$token) {
            $response = Http::timeout(15)
                ->withHeaders($auth)
                ->acceptJson()
                ->post("{$baseUrl}/api/v1/auth/token/refresh", [
                    'device_name' => 'smoke-test',
                ]);

            if (! $response->successful()) {
                return "HTTP {$response->status()}";
            }

            $newToken = $response->json('access_token');
            if (! is_string($newToken) || $newToken === '') {
                return 'Refresh sem access_token';
            }

            $token = $newToken;

            return null;
        });

        $auth = ['Authorization' => "Bearer {$token}"];

        $failed += $this->check('GET /api/v1/me', function () use ($baseUrl, $auth) {
            $response = Http::timeout(15)->withHeaders($auth)->acceptJson()->get("{$baseUrl}/api/v1/me");

            if (! $response->successful()) {
                return "HTTP {$response->status()}";
            }

            return null;
        });

        $contextId = (int) $this->option('context');
        $failed += $this->check('GET /api/v1/patient/links', function () use ($baseUrl, $auth, &$contextId) {
            $response = Http::timeout(15)->withHeaders($auth)->acceptJson()->get("{$baseUrl}/api/v1/patient/links");

            if (! $response->successful()) {
                return "HTTP {$response->status()}";
            }

            $links = $response->json('data');
            if (! is_array($links)) {
                return 'Resposta sem data[]';
            }

            if ($contextId <= 0 && $links !== []) {
                $contextId = (int) ($links[0]['id'] ?? 0);
            }

            return null;
        });

        if ($contextId > 0) {
            $contextHeaders = array_merge($auth, [
                'X-Active-Context' => (string) $contextId,
            ]);

            $failed += $this->check('GET /api/v1/patient/dashboard', function () use ($baseUrl, $contextHeaders) {
                $response = Http::timeout(15)
                    ->withHeaders($contextHeaders)
                    ->acceptJson()
                    ->get("{$baseUrl}/api/v1/patient/dashboard");

                if (! $response->successful()) {
                    return "HTTP {$response->status()}: ".($response->json('error') ?? $response->body());
                }

                return null;
            });
        } else {
            $this->warn('  [skip] /patient/dashboard — nenhum vínculo ativo para testar contexto');
        }

        $this->newLine();

        if ($failed > 0) {
            $this->error("Smoke test: {$failed} falha(s).");

            return self::FAILURE;
        }

        $this->info('Smoke test OK.');

        return self::SUCCESS;
    }

    private function check(string $label, callable $fn): int
    {
        $error = $fn();

        if ($error === null) {
            $this->line("  [ok] {$label}");

            return 0;
        }

        $this->error("  [falha] {$label} — {$error}");

        return 1;
    }
}
