<?php

namespace App\Services;

use App\Models\LoadLog;
use Illuminate\Support\Facades\DB;

class ProgressionService
{
    /**
     * Sugere a carga para o próximo treino com base no desempenho anterior e RPE (Percepção de Esforço).
     * Recurso Premium: NexShape Neural Progression.
     */
    public static function suggestLoad(int $userId, int $exerciseId, float $lastWeight, int $targetReps): array
    {
        // Buscar o histórico recente do exercício específico
        $lastLogs = LoadLog::where('user_id', $userId)
            ->where('exercise_id', $exerciseId)
            ->orderBy('log_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(3) // Pegar as últimas 3 séries registradas
            ->get();

        if ($lastLogs->isEmpty()) {
            return [
                'suggested_weight' => $lastWeight, 
                'message' => 'Inicie com a carga base para calibração.',
                'indicator' => 'maintain',
                'confidence' => 100,
                'max_safe_weight' => $lastWeight > 0 ? round($lastWeight * 1.5, 1) : 100,
            ];
        }

        $avgReps = $lastLogs->avg('reps_done');
        $avgRPE = $lastLogs->avg('rpe') ?: 8; // Default 8 if not recorded
        $allMetTarget = $lastLogs->every(fn($log) => $log->reps_done >= $targetReps);
        
        // Lógica de limite seguro (Evitar sobrecarga - máx +20% a +25%)
        $maxSafeWeight = round($lastWeight * 1.25, 1);

        // Lógica de Inteligência Neural (RPE-Based)
        $suggestion = [
            'suggested_weight' => $lastWeight,
            'message' => '📊 Consistência boa. Domine a técnica antes de evoluir.',
            'indicator' => 'maintain',
            'confidence' => 85,
            'max_safe_weight' => $maxSafeWeight,
        ];

        if ($allMetTarget) {
            // Se bateu as metas de repetições, olhamos o RPE
            if ($avgRPE <= 6) {
                // Muito leve
                $increase = max(2.5, round($lastWeight * 0.10 / 0.5) * 0.5);
                $suggestion = [
                    'suggested_weight' => $lastWeight + $increase,
                    'message' => '🚀 Carga muito leve. Aumento recomendado para estimular hipertrofia (+10%).',
                    'indicator' => 'increase',
                    'confidence' => 95,
                    'max_safe_weight' => $maxSafeWeight,
                ];
            } elseif ($avgRPE <= 8) {
                // Zona ideal
                $increase = max(1.0, round($lastWeight * 0.05 / 0.5) * 0.5);
                $suggestion = [
                    'suggested_weight' => $lastWeight + $increase,
                    'message' => '✅ Zona de hipertrofia ideal. Evolução sólida detectada (+5%).',
                    'indicator' => 'increase',
                    'confidence' => 90,
                    'max_safe_weight' => $maxSafeWeight,
                ];
            } elseif ($avgRPE >= 9.5) {
                // Limite
                $suggestion = [
                    'suggested_weight' => $lastWeight,
                    'message' => '⚖️ Limite de esforço atingido. Mantenha a carga para evitar falha do SNC.',
                    'indicator' => 'maintain',
                    'confidence' => 80,
                    'max_safe_weight' => $maxSafeWeight,
                ];
            }
        } else {
            // Não bateu as repetições
            if ($avgReps < ($targetReps * 0.7)) {
                // Muito abaixo da meta
                $decrease = round($lastWeight * 0.15 / 0.5) * 0.5;
                $suggestion = [
                    'suggested_weight' => max(0, $lastWeight - $decrease),
                    'message' => '⚠️ Sobrecarga detectada. Deload sugerido para recuperar amplitude de movimento.',
                    'indicator' => 'decrease',
                    'confidence' => 98,
                    'max_safe_weight' => $maxSafeWeight,
                ];
            } else {
                // Ligeiramente abaixo
                $suggestion = [
                    'suggested_weight' => $lastWeight,
                    'message' => '🔄 Quase lá! Concentre-se na execução perfeita com a mesma carga.',
                    'indicator' => 'maintain',
                    'confidence' => 75,
                    'max_safe_weight' => $maxSafeWeight,
                ];
            }
        }

        return $suggestion;
    }

    /**
     * Calcula o 1RM estimado usando a fórmula de Brzycki.
     */
    public static function calculateOneRepMax(float $weight, int $reps): float
    {
        if ($reps <= 0) return 0;
        if ($reps === 1) return $weight;
        
        // Brzycki Formula
        $oneRm = $weight / (1.0278 - (0.0278 * $reps));
        
        return round($oneRm, 2);
    }

    /**
     * Calcula o volume total de um treino ou série (Peso x Repetições).
     */
    public static function calculateVolume(float $weight, int $reps, int $sets = 1): float
    {
        return round($weight * $reps * $sets, 2);
    }
}
