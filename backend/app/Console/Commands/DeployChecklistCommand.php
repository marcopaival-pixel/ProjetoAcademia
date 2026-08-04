<?php

namespace App\Console\Commands;

use App\Models\DeployRelease;
use App\Services\BugSurgeonDeployGateService;
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
        $gate = app(BugSurgeonDeployGateService::class);
        $branch = getenv('BUGFIX_BRANCH') ?: $this->detectGitBranch();

        $this->info('=== Checklist pré-deploy ===');
        $this->line('Versão: ' . AppVersion::display());
        $this->line('Ambiente alvo: ' . $targetEnv);
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
            $this->error('  [falha] Banco: ' . $e->getMessage());
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
            $this->warn('  [!] ' . count($pending) . ' migration(s) pendente(s) — php artisan migrate');
            $failed++;
        } else {
            $this->line('  [ok] Migrations aplicadas');
        }

        if ($targetEnv === 'production') {
            $this->validateProductionEnvironment($failed);
        }

        if ($targetEnv === 'production' && Schema::hasTable('deploy_releases')) {
            $approved = DeployRelease::query()
                ->where('environment', DeployRelease::ENV_HOMOLOG)
                ->where('version', AppVersion::current())
                ->where('homolog_status', DeployRelease::HOMOLOG_APPROVED)
                ->where('status', DeployRelease::STATUS_SUCCESS)
                ->exists();

            if ($approved) {
                $this->line('  [ok] Homologação aprovada para v' . AppVersion::current());
            } else {
                $this->warn('  [!] Registre e aprove homologação em /admin/deploy');
                $failed++;
            }
        }

        if ($gate->isBugfixBranch($branch)) {
            $this->newLine();
            $this->info('=== Bug Surgeon gate (branch bugfix) ===');
            $gateTarget = $targetEnv === 'production' ? 'production' : 'homologacao';
            $gateErrors = $gate->validateDeploy($gateTarget, $branch);
            foreach ($gateErrors as $msg) {
                $this->error('  [falha] ' . $msg);
                $failed++;
            }
            if ($gateErrors === []) {
                $this->line('  [ok] Aprovações Bug Surgeon para ' . $gateTarget);
            }
        }

        $this->newLine();
        $this->comment('Validação manual recomendada:');
        foreach ([
            'Backup banco + arquivos',
            'composer install / npm install (se package.json ou composer.json mudou)',
            'Login, financeiro, permissões, 2 empresas distintas',
            'Logs, filas e e-mails pós-deploy',
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

    private function validateProductionEnvironment(int &$failed): void
    {
        $this->newLine();
        $this->info('=== Validação ambiente produção ===');

        if (config('app.env') !== 'production') {
            $this->warn('  [!] APP_ENV não é production (valor atual: '.config('app.env').')');
            $failed++;
        } else {
            $this->line('  [ok] APP_ENV=production');
        }

        if (config('app.debug')) {
            $this->error('  [falha] APP_DEBUG=true em produção');
            $failed++;
        } else {
            $this->line('  [ok] APP_DEBUG=false');
        }

        foreach ([
            'MP_WEBHOOK_SECRET' => 'Mercado Pago webhook',
            'OMNI_WEBHOOK_SECRET' => 'Omnichannel webhook',
        ] as $envKey => $label) {
            if (blank(env($envKey))) {
                $this->error("  [falha] {$envKey} ausente ({$label})");
                $failed++;
            } else {
                $this->line("  [ok] {$envKey} definido");
            }
        }

        if (! config('session.encrypt')) {
            $this->warn('  [!] SESSION_ENCRYPT=false — recomendado true em HTTPS');
            $failed++;
        } else {
            $this->line('  [ok] SESSION_ENCRYPT=true');
        }

        $redisDrivers = ['redis'];
        foreach ([
            'CACHE_STORE' => config('cache.default'),
            'SESSION_DRIVER' => config('session.driver'),
            'QUEUE_CONNECTION' => config('queue.default'),
        ] as $label => $driver) {
            if (! in_array($driver, $redisDrivers, true)) {
                $this->warn("  [!] {$label}={$driver} — recomendado redis em produção");
            } else {
                $this->line("  [ok] {$label} usa redis");
            }
        }

        if (in_array(config('logging.default'), ['stack', 'single'], true) && config('logging.channels.single.level') === 'debug') {
            $this->warn('  [!] LOG_LEVEL=debug em produção');
            $failed++;
        }
    }

    private function detectGitBranch(): ?string
    {
        foreach ([base_path('../.git/HEAD'), base_path('.git/HEAD')] as $head) {
            if (! is_file($head)) {
                continue;
            }
            $ref = trim((string) file_get_contents($head));
            if (str_starts_with($ref, 'ref: ')) {
                return basename(trim(substr($ref, 5)));
            }
        }

        return null;
    }
}
