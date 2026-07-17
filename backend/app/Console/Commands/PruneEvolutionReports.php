<?php

namespace App\Console\Commands;

use App\Models\EvolutionReport;
use Illuminate\Console\Command;

class PruneEvolutionReports extends Command
{
    protected $signature = 'evolution:prune-reports
        {--days=180 : Quantidade de dias de relatórios a manter}
        {--force : Executar sem confirmação}';

    protected $description = 'Remove relatórios de evolução corporal e logs vinculados após o prazo de retenção.';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $force = (bool) $this->option('force');

        if (! $force && ! $this->confirm("Remover relatórios de evolução com mais de {$days} dias?")) {
            $this->info('Operação cancelada.');
            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days);

        $count = EvolutionReport::query()
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Relatórios de evolução removidos: {$count}");

        return self::SUCCESS;
    }
}
