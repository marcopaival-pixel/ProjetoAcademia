<?php

namespace App\Services;

use App\Models\LoadLog;
use Illuminate\Support\Facades\DB;

class ProgressionService
{
    /**
     * Sugere a carga para o próximo treino com base no desempenho anterior.
     */
    public static function suggestLoad(int $userId, int $exerciseId, float $lastWeight, int $targetReps): array
    {
        // Buscar o histórico recente
        $lastSession = LoadLog::where('user_id', $userId)
            ->where('exercise_id', $exerciseId)
            ->orderBy('log_date', 'desc')
            ->limit(3) // Pegar as últimas 3 séries do último treino
            ->get();

        if ($lastSession->isEmpty()) {
            return ['suggested_weight' => $lastWeight, 'message' => 'Mantenha a carga inicial.'];
        }

        // Verificar se todas as reps foram batidas (ou superadas)
        $avgReps = $lastSession->avg('reps_done');
        $allMetTarget = $lastSession->every(fn($log) => $log->reps_done >= $targetReps);

        if ($allMetTarget) {
            // Se bateu as metas, sugerir aumento
            // Lógica simples: +2kg para pesos leves, +5% para pesos pesados
            $increase = ($lastWeight < 20) ? 1.0 : ($lastWeight * 0.05);
            $suggested = round(($lastWeight + $increase) / 0.5) * 0.5; // Arredondar para 0.5kg
            
            return [
                'suggested_weight' => $suggested,
                'message' => '🚀 Desempenho excelente! Sugerimos aumentar a carga.',
                'indicator' => 'increase'
            ];
        }

        if ($avgReps < ($targetReps * 0.8)) {
            // Se ficou muito abaixo (menos de 80% das reps), sugerir deload leve
            $suggested = round(($lastWeight * 0.9) / 0.5) * 0.5;
            return [
                'suggested_weight' => $suggested,
                'message' => '⚖️ Ajuste técnico: Sugarimos reduzir levemente para focar na forma.',
                'indicator' => 'decrease'
            ];
        }

        return [
            'suggested_weight' => $lastWeight,
            'message' => '📊 Consistência boa. Tente dominar esta carga antes de aumentar.',
            'indicator' => 'maintain'
        ];
    }
}
