<?php

namespace App\Console\Commands;

use App\Models\AIOrchestratorLog;
use App\Services\Operations\AlertDispatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AiCostAuditCommand extends Command
{
    protected $signature = 'ai:cost-audit {--days=1 : Dias para analisar}';

    protected $description = 'Audita consumo de IA (USD/tokens) e dispara alertas se limites forem excedidos';

    public function handle(AlertDispatcher $alerts): int
    {
        $days = (int) $this->option('days');
        $startDate = now()->subDays($days)->startOfDay();

        $totalCost = (float) AIOrchestratorLog::where('created_at', '>=', $startDate)->sum('cost_usd');
        $totalTokens = (int) AIOrchestratorLog::where('created_at', '>=', $startDate)->sum('total_tokens');
        $totalRequests = (int) AIOrchestratorLog::where('created_at', '>=', $startDate)->count();
        $errors = (int) AIOrchestratorLog::where('created_at', '>=', $startDate)->whereIn('status', ['error', 'limit_reached'])->count();
        $errorRate = $totalRequests > 0 ? ($errors / $totalRequests) * 100 : 0;

        $this->info("IA Audit ({$days}d): USD \${$totalCost} | Tokens {$totalTokens} | Requests {$totalRequests} | Error rate ".round($errorRate, 1).'%');

        $globalLimit = (float) config('ai.limits.daily_usd_global', 0);
        if ($globalLimit > 0 && $days <= 1 && $totalCost >= $globalLimit) {
            $alerts->dispatchCritical(
                'Limite diário global de IA excedido',
                "Custo USD hoje: \${$totalCost} (limite: \${$globalLimit})",
                ['total_cost' => $totalCost, 'limit' => $globalLimit],
                'ai_cost_global_'.today()->toDateString()
            );
            $this->warn('Alerta: limite global USD excedido.');
        }

        $errorThreshold = (float) config('ai.limits.error_rate_alert_percent', 5);
        if ($errorRate >= $errorThreshold && $totalRequests >= 10) {
            $alerts->dispatchCritical(
                'Taxa de erro IA elevada',
                "Taxa de erro/limites: ".round($errorRate, 1)."% ({$errors}/{$totalRequests})",
                ['error_rate' => $errorRate, 'errors' => $errors],
                'ai_error_rate_'.today()->toDateString()
            );
            $this->warn('Alerta: taxa de erro IA elevada.');
        }

        $clinicLimit = (float) config('ai.limits.daily_usd_per_clinic', 0);
        if ($clinicLimit > 0 && $days <= 1) {
            $offenders = AIOrchestratorLog::select('clinic_id', DB::raw('sum(cost_usd) as total'))
                ->whereDate('created_at', today())
                ->whereNotNull('clinic_id')
                ->groupBy('clinic_id')
                ->having('total', '>=', $clinicLimit)
                ->get();

            foreach ($offenders as $row) {
                $alerts->dispatchCritical(
                    'Limite diário IA por clínica excedido',
                    "Clínica #{$row->clinic_id}: \${$row->total} (limite: \${$clinicLimit})",
                    ['clinic_id' => $row->clinic_id, 'cost' => $row->total],
                    "ai_clinic_{$row->clinic_id}_".today()->toDateString()
                );
                $this->warn("Alerta: clínica #{$row->clinic_id} excedeu limite.");
            }
        }

        return self::SUCCESS;
    }
}
