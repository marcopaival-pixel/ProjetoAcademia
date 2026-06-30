<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurgePulseDataCommand extends Command
{
    protected $signature = 'app:purge-pulse
                            {--days= : Retenção em dias (default: config observability)}
                            {--force : Executar sem confirmação}';

    protected $description = 'Remove entradas antigas do Laravel Pulse (pulse_entries, pulse_values, pulse_aggregates).';

    public function handle(): int
    {
        $days = $this->option('days') !== null
            ? max(1, (int) $this->option('days'))
            : (int) config('observability.retention_days.pulse', 7);

        if (! $this->option('force') && ! $this->confirm("Remover dados Pulse anteriores a {$days} dias?")) {
            $this->info('Operação cancelada.');

            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days)->getTimestamp();
        $total = 0;

        $total += $this->purgeTable('pulse_entries', 'timestamp', $cutoff);
        $total += $this->purgeTable('pulse_values', 'timestamp', $cutoff);
        $total += $this->purgeTable('pulse_aggregates', 'bucket', $cutoff);

        $this->info("Limpeza Pulse concluída — {$total} registo(s) removido(s) (> {$days} dias).");

        return self::SUCCESS;
    }

    private function purgeTable(string $table, string $column, int $cutoff): int
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            $this->warn("  [skip] {$table} — tabela/coluna ausente");

            return 0;
        }

        $removed = 0;

        do {
            $batch = DB::table($table)
                ->where($column, '<', $cutoff)
                ->limit(2000)
                ->delete();
            $removed += $batch;
        } while ($batch > 0);

        if ($removed > 0) {
            $this->line("  [ok] {$table}: {$removed} removido(s)");
        } else {
            $this->line("  [ok] {$table}: nada a remover");
        }

        return $removed;
    }
}
