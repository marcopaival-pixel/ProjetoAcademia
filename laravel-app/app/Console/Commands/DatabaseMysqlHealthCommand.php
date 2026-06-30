<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseMysqlHealthCommand extends Command
{
    protected $signature = 'app:db:mysql-health
                            {--json : Saída JSON}
                            {--fail-on-warnings : Exit 1 se buffer pool baixo ou slow log off em produção}';

    protected $description = 'Diagnóstico read-only de variáveis MySQL/MariaDB (XAMPP/produção).';

    /** @var list<string> */
    private array $variables = [
        'innodb_buffer_pool_size',
        'slow_query_log',
        'long_query_time',
        'max_connections',
        'version',
    ];

    public function handle(): int
    {
        if (config('database.default') !== 'mysql') {
            $this->warn('Conexão default não é mysql — diagnóstico ignorado.');

            return self::SUCCESS;
        }

        $rows = [];
        foreach ($this->variables as $variable) {
            $safe = str_replace("'", "''", $variable);
            $result = DB::select("SHOW VARIABLES LIKE '{$safe}'");
            $rows[$variable] = $result[0]->Value ?? null;
        }

        $bufferPoolBytes = (int) ($rows['innodb_buffer_pool_size'] ?? 0);
        $bufferPoolMb = round($bufferPoolBytes / 1024 / 1024, 1);
        $slowLogOn = strtolower((string) ($rows['slow_query_log'] ?? 'off')) === 'on';
        $warnings = [];

        if ($bufferPoolMb > 0 && $bufferPoolMb < 64) {
            $warnings[] = "innodb_buffer_pool_size baixo ({$bufferPoolMb} MB) — recomendado ≥128 MB em XAMPP (my.ini)";
        }

        if (! $slowLogOn && app()->environment('production')) {
            $warnings[] = 'slow_query_log desativado em produção — ativar para diagnóstico de queries lentas';
        }

        $pulseRows = null;
        if (Schema::hasTable('pulse_entries')) {
            $pulseRows = (int) DB::table('pulse_entries')->count();
            if ($pulseRows > 50_000) {
                $warnings[] = "pulse_entries com {$pulseRows} linhas — confirmar app:purge-pulse no scheduler";
            }
        }

        $report = [
            'generated_at' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'variables' => $rows,
            'buffer_pool_mb' => $bufferPoolMb,
            'slow_query_log' => $slowLogOn,
            'pulse_entries_count' => $pulseRows,
            'warnings' => $warnings,
        ];

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $this->exitCode($warnings);
        }

        $this->info('=== MySQL / MariaDB — diagnóstico ===');
        $this->line('Ambiente: '.$report['environment']);
        $this->line('Versão: '.($rows['version'] ?? '?'));
        $this->line('innodb_buffer_pool_size: '.$bufferPoolMb.' MB');
        $this->line('slow_query_log: '.($slowLogOn ? 'ON' : 'OFF'));
        $this->line('long_query_time: '.($rows['long_query_time'] ?? '?').' s');
        $this->line('max_connections: '.($rows['max_connections'] ?? '?'));

        if ($pulseRows !== null) {
            $this->line("pulse_entries: {$pulseRows} linha(s)");
        }

        if ($warnings === []) {
            $this->info('Sem avisos de infraestrutura.');
        } else {
            foreach ($warnings as $warning) {
                $this->warn('⚠ '.$warning);
            }
        }

        $this->newLine();
        $this->line('XAMPP: ver docs/MONITORAMENTO.md secção MySQL');

        return $this->exitCode($warnings);
    }

    /**
     * @param  list<string>  $warnings
     */
    private function exitCode(array $warnings): int
    {
        if ($warnings !== [] && $this->option('fail-on-warnings')) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
