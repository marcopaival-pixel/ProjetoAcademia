<?php

namespace App\Console\Commands;

use App\Models\EvolutionSessionAnalysis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneEvolutionSessionAnalyses extends Command
{
    protected $signature = 'evolution:prune-session-analyses
        {--months=12 : Quantidade de meses de análises a manter}
        {--per-user=100 : Máximo de análises recentes por usuário}
        {--force : Executar sem confirmação}';

    protected $description = 'Remove análises antigas/excedentes de sessões de evolução corporal.';

    public function handle(): int
    {
        $months = max(1, (int) $this->option('months'));
        $perUser = max(1, (int) $this->option('per-user'));
        $force = (bool) $this->option('force');

        if (! $force && ! $this->confirm("Remover análises com mais de {$months} meses e manter no máximo {$perUser} por usuário?")) {
            $this->info('Operação cancelada.');
            return self::SUCCESS;
        }

        $cutoff = now()->subMonths($months)->toDateString();
        $oldCount = EvolutionSessionAnalysis::whereDate('session_date', '<', $cutoff)->delete();

        $excessCount = 0;
        $userIds = EvolutionSessionAnalysis::query()
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $keepIds = EvolutionSessionAnalysis::where('user_id', $userId)
                ->orderByDesc('session_date')
                ->orderByDesc('created_at')
                ->limit($perUser)
                ->pluck('id');

            $deleted = EvolutionSessionAnalysis::where('user_id', $userId)
                ->whereNotIn('id', $keepIds)
                ->delete();

            $excessCount += $deleted;
        }

        $this->info("Análises antigas removidas: {$oldCount}");
        $this->info("Análises excedentes removidas: {$excessCount}");

        return self::SUCCESS;
    }
}
