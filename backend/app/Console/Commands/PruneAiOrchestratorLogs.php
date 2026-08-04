<?php

namespace App\Console\Commands;

use App\Models\AIOrchestratorLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class PruneAiOrchestratorLogs extends Command
{
    protected $signature = 'ai:prune-orchestrator-logs
        {--days= : Dias de retencao (padrao: config services.ai.log_retention_days)}
        {--force : Executar sem confirmacao}';

    protected $description = 'Remove logs antigos do orquestrador de IA para reduzir retencao de PII.';

    public function handle(): int
    {
        if (! Schema::hasTable('ai_orchestrator_logs')) {
            $this->warn('Tabela ai_orchestrator_logs nao encontrada.');

            return self::SUCCESS;
        }

        $days = (int) ($this->option('days') ?: config('services.ai.log_retention_days', 90));
        $days = max(7, $days);
        $force = (bool) $this->option('force');

        if (! $force && ! $this->confirm("Remover logs de IA com mais de {$days} dias?")) {
            $this->info('Operacao cancelada.');

            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days);

        $deleted = AIOrchestratorLog::query()
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Registros removidos: {$deleted}.");

        return self::SUCCESS;
    }
}
