<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class SmokeTestCommand extends Command
{
    protected $signature = 'app:smoke:test {--target=homologacao : homologacao ou production}';

    protected $description = 'Smoke test pós-deploy: rotas críticas, RBAC e variáveis de ambiente.';

    public function handle(): int
    {
        $target = (string) $this->option('target');
        $failed = 0;

        $this->info('=== Smoke test ===');
        $this->line("Alvo: {$target}");
        $this->newLine();

        try {
            DB::connection()->getPdo();
            $this->line('  [ok] Banco acessível');
        } catch (\Throwable $e) {
            $this->error('  [falha] Banco: '.$e->getMessage());
            $failed++;
        }

        foreach (['up' => '/up', 'health' => '/health', 'api_health' => '/api/v1/health', 'login' => '/login', 'home' => '/'] as $label => $path) {
            try {
                $request = \Illuminate\Http\Request::create($path, 'GET');
                $response = app()->handle($request);
                $status = $response->getStatusCode();
                if ($status >= 200 && $status < 500) {
                    $this->line("  [ok] {$label} (HTTP {$status})");
                } else {
                    $this->warn("  [!] {$label} retornou HTTP {$status}");
                    $failed++;
                }
            } catch (\Throwable $e) {
                $this->warn("  [!] {$label}: ".$e->getMessage());
                $failed++;
            }
        }

        $requiredRoles = ['admin', 'professional', 'representative', 'aluno', 'paciente'];
        $missingRoles = [];
        foreach ($requiredRoles as $roleName) {
            if (! Role::query()->where('name', $roleName)->exists()) {
                $missingRoles[] = $roleName;
            }
        }
        if ($missingRoles === []) {
            $this->line('  [ok] Perfis RBAC base presentes');
        } else {
            $this->warn('  [!] Perfis em falta: '.implode(', ', $missingRoles).' — php artisan db:seed --class=RolesAndPermissionsSeeder');
            $failed++;
        }

        $criticalRoutes = ['checkout.process', 'patient.reports.index', 'representative.dashboard', 'professional.finance.dashboard'];
        foreach ($criticalRoutes as $routeName) {
            if (Route::has($routeName)) {
                $this->line("  [ok] Rota {$routeName}");
            } else {
                $this->warn("  [!] Rota ausente: {$routeName}");
                $failed++;
            }
        }

        if ($target === 'production') {
            if (config('app.debug') === false) {
                $this->line('  [ok] APP_DEBUG=false');
            } else {
                $this->warn('  [!] APP_DEBUG deve ser false em produção');
                $failed++;
            }

            if ((string) config('app.env') === 'production') {
                $this->line('  [ok] APP_ENV=production');
            } else {
                $this->warn('  [!] APP_ENV deve ser production');
                $failed++;
            }

            if (trim((string) env('MP_WEBHOOK_SECRET')) !== '') {
                $this->line('  [ok] MP_WEBHOOK_SECRET definido');
            } else {
                $this->warn('  [!] MP_WEBHOOK_SECRET obrigatório em produção');
                $failed++;
            }

            $previousEnv = config('app.env');
            config(['app.env' => 'production']);
            try {
                $request = \Illuminate\Http\Request::create('/demo/start', 'GET');
                $response = app()->handle($request);
                if ($response->getStatusCode() === 404) {
                    $this->line('  [ok] Demo bloqueada em produção');
                } else {
                    $this->warn('  [!] /demo/start deveria retornar 404 em produção (HTTP '.$response->getStatusCode().')');
                    $failed++;
                }
            } catch (\Throwable $e) {
                $this->warn('  [!] Demo check: '.$e->getMessage());
                $failed++;
            } finally {
                config(['app.env' => $previousEnv]);
            }
        }

        $this->newLine();
        if ($failed > 0) {
            $this->error("Smoke test: {$failed} falha(s).");

            return self::FAILURE;
        }

        $this->info('Smoke test OK.');

        return self::SUCCESS;
    }
}
