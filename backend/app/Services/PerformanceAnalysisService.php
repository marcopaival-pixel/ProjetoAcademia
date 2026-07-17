<?php

namespace App\Services;

use App\Models\User;
use App\Models\HealthMetric;
use App\Models\ExerciseEntry;
use Carbon\Carbon;

class PerformanceAnalysisService
{
    /**
     * Calcula o status geral de prontidão e risco do usuário.
     */
    public function getUserStatus(User $user)
    {
        $acwr = $this->calculateACWR($user);
        $recovery = $this->getLatestRecovery($user);
        
        $riskLevel = $this->determineRiskLevel($acwr, $recovery);

        return [
            'acwr' => round($acwr, 2),
            'recovery_score' => $recovery,
            'risk_level' => $riskLevel['status'], // low, moderate, high, danger
            'recommendation' => $riskLevel['message'],
            'indicators' => [
                'fatigue' => $acwr > 1.3 ? 'high' : 'normal',
                'readiness' => $recovery > 70 ? 'ready' : 'needs_rest'
            ]
        ];
    }

    /**
     * Calcula o Acute:Chronic Workload Ratio.
     * Carga Aguda (7 dias) / Carga Crônica (28 dias)
     */
    public function calculateACWR(User $user): float
    {
        $acuteLoad = $this->getAverageLoad($user, 7);
        $chronicLoad = $this->getAverageLoad($user, 28);

        if ($chronicLoad == 0) return 1.0;

        return $acuteLoad / $chronicLoad;
    }

    /**
     * Calcula a carga média em um período de dias.
     * Carga = Duração (min) * Intensidade (RPE ou Calorias como fallback)
     */
    private function getAverageLoad(User $user, int $days): float
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days - 1);

        $totalLoad = ExerciseEntry::where('user_id', $user->id)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->get()
            ->sum(function ($entry) {
                // Usamos a duração como base. Se houver calorias, usamos como multiplicador de intensidade.
                $intensity = ($entry->calories_burned ?? 100) / 100; 
                return $entry->duration_min * $intensity;
            });

        return $totalLoad / $days;
    }

    /**
     * Obtém a última pontuação de recuperação baseada em HRV e Sono.
     */
    private function getLatestRecovery(User $user): int
    {
        $lastMetrics = HealthMetric::where('user_id', $user->id)
            ->whereIn('type', [HealthMetric::TYPE_RECOVERY, HealthMetric::TYPE_HRV, HealthMetric::TYPE_SLEEP_QUALITY])
            ->where('recorded_at', '>=', now()->subDays(2))
            ->recent()
            ->get();

        if ($lastMetrics->isEmpty()) return 70; // Default (neutro)

        // Média ponderada das métricas de prontidão
        return (int) $lastMetrics->avg('value');
    }

    /**
     * Determina o nível de risco baseado no cruzamento de Carga e Recuperação.
     */
    private function determineRiskLevel(float $acwr, int $recovery): array
    {
        // ACWR Sweet Spot: 0.8 a 1.3
        // Danger Zone: > 1.5 (Risco de lesão aumenta muito)
        
        if ($acwr > 1.5) {
            return [
                'status' => 'danger',
                'message' => 'Risco crítico de lesão. Reduza drasticamente o volume ou tire um dia de descanso.'
            ];
        }

        if ($acwr > 1.3 || $recovery < 40) {
            return [
                'status' => 'high',
                'message' => 'Fadiga acumulada alta. Priorize sono e treinos regenerativos.'
            ];
        }

        if ($acwr < 0.8) {
            return [
                'status' => 'moderate',
                'message' => 'Carga abaixo do ideal. Você pode aumentar a intensidade gradualmente.'
            ];
        }

        return [
            'status' => 'low',
            'message' => 'Sweet Spot! Seu corpo está absorvendo bem a carga. Continue assim.'
        ];
    }
}
