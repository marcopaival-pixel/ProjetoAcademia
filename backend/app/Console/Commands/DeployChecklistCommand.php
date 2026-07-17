<?php

namespace App\Console\Commands;

use App\Models\DeployRelease;
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
}
