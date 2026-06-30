<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseHealthReportCommand extends Command
{
    protected $signature = 'app:db:health-report
                            {--json : Saída em JSON}
                            {--fail-on-issues : Exit code 1 se houver migrations pendentes ou órfãos}';

    protected $description = 'Relatório consolidado de saúde do banco (migrations, órfãos, índices).';

    public function handle(): int
    {
        if (! Schema::hasTable('users')) {
            $this->error('Base de dados inacessível ou migrações pendentes.');

            return self::FAILURE;
        }

        $pendingMigrations = $this->countPendingMigrations();
        $orphanSummary = DatabaseOrphansCommand::audit();

        $report = [
            'generated_at' => now()->toIso8601String(),
            'database' => DB::connection()->getDatabaseName(),
            'migrations_pending' => $pendingMigrations,
            'orphans' => [
                'total' => $orphanSummary['total_orphans'],
                'failed_checks' => $orphanSummary['failed_checks'],
                'skipped_checks' => $orphanSummary['skipped'],
                'details' => array_values(array_filter(
                    $orphanSummary['results'],
                    fn (array $r) => $r['status'] === 'orphans'
                )),
            ],
            'commands' => [
                'index_explain' => 'php artisan app:db:index-explain',
                'orphans' => 'php artisan app:db:orphans',
                'dead_columns' => 'php artisan app:db:dead-columns',
            ],
        ];

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $this->exitCode($pendingMigrations, $orphanSummary['failed_checks']);
        }

        $this->info('=== Saúde do Banco — NexShape ===');
        $this->newLine();
        $this->line('Base: '.$report['database']);
        $this->line('Gerado: '.$report['generated_at']);
        $this->newLine();

        $migrationStatus = $pendingMigrations === 0 ? '✔ OK' : "⚠ {$pendingMigrations} pendente(s)";
        $this->line("Migrations: {$migrationStatus}");

        $orphanStatus = $orphanSummary['total_orphans'] === 0
            ? '✔ OK'
            : "⚠ {$orphanSummary['total_orphans']} órfão(s) em {$orphanSummary['failed_checks']} check(s)";
        $this->line("Integridade (FKs críticas): {$orphanStatus}");

        foreach ($orphanSummary['results'] as $row) {
            if ($row['status'] === 'orphans') {
                $this->line("  - {$row['key']}: {$row['count']} órfão(s)");
            }
        }

        $this->newLine();
        $this->line('Índices / EXPLAIN: php artisan app:db:index-explain');
        $this->line('Colunas mortas (heurística): php artisan app:db:dead-columns');
        $this->line('Doc AS-IS: laravel-app/docs/dicionario_dados.md');
        $this->line('Agente Cursor: @agente-especialista-banco-dados');

        return $this->exitCode($pendingMigrations, $orphanSummary['failed_checks']);
    }

    private function countPendingMigrations(): int
    {
        $files = glob(database_path('migrations/*.php')) ?: [];
        if ($files === []) {
            return 0;
        }

        if (! Schema::hasTable('migrations')) {
            return count($files);
        }

        $ran = DB::table('migrations')->pluck('migration')->all();
        $pending = 0;

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if (! in_array($name, $ran, true)) {
                $pending++;
            }
        }

        return $pending;
    }

    private function exitCode(int $pendingMigrations, int $failedOrphanChecks): int
    {
        $hasIssues = $pendingMigrations > 0 || $failedOrphanChecks > 0;

        if ($hasIssues && $this->option('fail-on-issues')) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
