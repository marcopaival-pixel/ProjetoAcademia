<?php

namespace App\Console\Commands;

use App\Models\DeployRelease;
use App\Services\Operations\SystemHealthService;
use App\Support\AppVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeployChecklistCommand extends Command
{
    protected $signature = 'app:deploy:checklist {--target=production : homologacao ou production}';

    protected $description = 'Checklist pré-deploy: versão, migrations pendentes, banco e homologação.';

    public function handle(): int
    {
        $targetEnv = (string) $this->option('target');
        $failed = 0;
        $migrator = app('migrator');

        $this->info('=== Checklist pré-deploy ===');
        $this->line('Versão: '.AppVersion::display());
        $this->line('Ambiente alvo: '.$targetEnv);
        $this->newLine();

        if ((string) config('app.key') === '') {
            $this->error('  [falha] APP_KEY não definida');
            $failed++;
        } else {
            $this->line('  [ok] APP_KEY definida');
        }

        try {
            DB::connection()->getPdo();
            $this->line('  [ok] Conexão com banco');
        } catch (\Throwable $e) {
            $this->error('  [falha] Banco: '.$e->getMessage());
            $failed++;
        }

        if (! Schema::hasTable('deploy_releases')) {
            $this->warn('  [!] Tabela deploy_releases ausente — php artisan migrate');
            $failed++;
        } else {
            $this->line('  [ok] Tabela deploy_releases');
        }

        $files = $migrator->getMigrationFiles($migrator->paths());
        $ran = $migrator->getRepository()->getRan();
        $pending = array_diff(array_keys($files), $ran);

        if ($pending !== []) {
            $this->warn('  [!] '.count($pending).' migration(s) pendente(s) — php artisan migrate');
            $failed++;
        } else {
            $this->line('  [ok] Migrations aplicadas');
        }

        if ($targetEnv === 'production' && Schema::hasTable('deploy_releases')) {
            $approved = DeployRelease::query()
                ->where('environment', DeployRelease::ENV_HOMOLOG)
                ->where('version', AppVersion::current())
                ->where('homolog_status', DeployRelease::HOMOLOG_APPROVED)
                ->where('status', DeployRelease::STATUS_SUCCESS)
                ->exists();

            if ($approved) {
                $this->line('  [ok] Homologação aprovada para v'.AppVersion::current());
            } else {
                $this->warn('  [!] Registre e aprove homologação em /admin/deploy');
                $failed++;
            }
        }

        try {
            $health = app(SystemHealthService::class)->checkAll();
            if ($health['status'] === 'healthy') {
                $this->line('  [ok] SystemHealthService: healthy');
            } else {
                $this->warn('  [!] SystemHealthService: '.$health['status']);
                $failed++;
            }
        } catch (\Throwable $e) {
            $this->warn('  [!] Health check interno: '.$e->getMessage());
            $failed++;
        }

        foreach (['up' => '/up', 'health' => '/health', 'api_health' => '/api/v1/health'] as $label => $path) {
            try {
                $request = \Illuminate\Http\Request::create($path, 'GET');
                $response = app()->handle($request);
                $status = $response->getStatusCode();
                if ($status >= 200 && $status < 500) {
                    $this->line("  [ok] Endpoint {$label} (HTTP {$status})");
                } else {
                    $this->warn("  [!] Endpoint {$label} retornou HTTP {$status}");
                    $failed++;
                }
            } catch (\Throwable $e) {
                $this->warn("  [!] Endpoint {$label}: ".$e->getMessage());
                $failed++;
            }
        }

        if (config('sentry.dsn')) {
            $this->line('  [ok] Sentry DSN configurado');
        } else {
            $this->warn('  [!] SENTRY_LARAVEL_DSN não definido (opcional)');
        }

        if (trim((string) config('mail.operational_alert.address')) !== '') {
            $this->line('  [ok] OPERATIONAL_ALERT_EMAIL configurado');
        } elseif ($targetEnv === 'production') {
            $this->warn('  [!] OPERATIONAL_ALERT_EMAIL não definido');
            $failed++;
        } else {
            $this->warn('  [!] OPERATIONAL_ALERT_EMAIL não definido (obrigatório em produção)');
        }

        if ($targetEnv === 'production') {
            if (config('app.debug') === false) {
                $this->line('  [ok] APP_DEBUG=false');
            } else {
                $this->warn('  [!] APP_DEBUG deve ser false em produção');
                $failed++;
            }

            if ((string) config('app.env') === 'production') {
                $this->line('  [ok] APP_ENV=production');
            } else {
                $this->warn('  [!] APP_ENV deve ser production (atual: '.config('app.env').')');
                $failed++;
            }

            if (trim((string) env('MP_WEBHOOK_SECRET')) !== '') {
                $this->line('  [ok] MP_WEBHOOK_SECRET definido');
            } else {
                $this->warn('  [!] MP_WEBHOOK_SECRET obrigatório em produção');
                $failed++;
            }

            if (trim((string) env('APP_PUBLIC_URL')) !== '') {
                $this->line('  [ok] APP_PUBLIC_URL definido');
            } else {
                $this->warn('  [!] APP_PUBLIC_URL não definido (Mercado Pago / webhooks)');
                $failed++;
            }
        }

        $this->newLine();
        $this->comment('Validação manual recomendada:');
        foreach ([
            'php artisan app:release:verify --target='.$targetEnv.' --with-tests',
            'Backup banco + arquivos + teste de restore',
            'composer install / npm run build (se dependências mudaram)',
            'Login (5 perfis), checkout sandbox, webhook MP, 2 clínicas distintas',
            'Queue worker + schedule:run (Supervisor)',
            'UptimeRobot em /up + Sentry (opcional)',
        ] as $step) {
            $this->line("  • {$step}");
        }

        if ($failed > 0) {
            $this->error("Checklist: {$failed} alerta(s).");

            return self::FAILURE;
        }

        $this->info('Checklist automático OK.');

        return self::SUCCESS;
    }
}
