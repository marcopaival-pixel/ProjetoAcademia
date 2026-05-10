<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\BodyAssessment;
use App\Models\WeightEntry;
use Carbon\Carbon;

class IntelligenceMotorService
{
    /**
     * Calcula o Score de Saúde (0-100) baseado em múltiplos fatores.
     */
    public function calculateHealthScore(User $user): int
    {
        $profile = $user->profile;
        if (!$profile) return 50;

        $score = 70; // Base score

        // 1. BMI Factor
        $bmi = $this->calculateBMI($user);
        if ($bmi) {
            if ($bmi >= 18.5 && $bmi <= 24.9) $score += 10;
            elseif ($bmi >= 25 && $bmi <= 29.9) $score -= 5;
            else $score -= 15;
        }

        // 2. Activity Level
        $score += match($profile->activity_level) {
            'active', 'very_active' => 10,
            'moderate' => 5,
            'sedentary' => -5,
            default => 0
        };

        // 3. Consistency (Water, Food, Training)
        $waterLogCount = $user->waterEntries()->where('entry_date', '>=', now()->subDays(7))->count();
        if ($waterLogCount >= 5) $score += 5;

        $workoutCount = $user->exerciseEntries()->where('entry_date', '>=', now()->subDays(7))->count();
        if ($workoutCount >= 3) $score += 5;

        // 4. Health Risks
        if ($profile->has_disease) $score -= 10;
        if ($profile->uses_medication) $score -= 5;

        return (int) max(0, min(100, $score));
    }

    /**
     * Calcula o IMC atual do usuário.
     */
    public function calculateBMI(User $user): ?float
    {
        $profile = $user->profile;
        $lastWeight = $user->weightEntries()->latest('weighed_at')->first();

        if (!$profile || !$profile->height_cm || !$lastWeight) return null;

        $heightM = $profile->height_cm / 100;
        return round($lastWeight->weight_kg / ($heightM * $heightM), 1);
    }

    /**
     * Gera uma previsão de evolução (Data estimada para atingir o peso alvo).
     */
    public function predictEvolution(User $user): array
    {
        $profile = $user->profile;
        $lastWeight = $user->weightEntries()->latest('weighed_at')->first();

        if (!$profile || !$profile->target_weight_kg || !$lastWeight) {
            return ['possible' => false, 'message' => 'Dados insuficientes para previsão.'];
        }

        $currentWeight = $lastWeight->weight_kg;
        $targetWeight = $profile->target_weight_kg;
        $diff = abs($currentWeight - $targetWeight);

        if ($diff < 0.5) {
            return ['possible' => true, 'message' => 'Meta atingida ou muito próxima!', 'days' => 0];
        }

        // Estimativa conservadora: 0.5kg por semana para perda, 0.25kg para ganho
        $weeklyRate = ($targetWeight < $currentWeight) ? 0.6 : 0.3;
        $weeksNeeded = $diff / $weeklyRate;
        $daysNeeded = (int) ($weeksNeeded * 7);

        $estimatedDate = now()->addDays($daysNeeded);

        return [
            'possible' => true,
            'days' => $daysNeeded,
            'date' => $estimatedDate->format('d/m/Y'),
            'weekly_rate' => $weeklyRate,
            'message' => "Estimativa de " . round($weeksNeeded, 1) . " semanas para atingir a meta."
        ];
    }

    /**
     * Detecta inconsistências ou metas perigosas.
     */
    public function detectRisks(User $user): array
    {
        $profile = $user->profile;
        $lastWeight = $user->weightEntries()->latest('weighed_at')->first();
        $risks = [];

        if (!$profile || !$lastWeight) return $risks;

        $bmi = $this->calculateBMI($user);

        // Meta de peso perigosa (IMC muito baixo)
        if ($profile->target_weight_kg) {
            $heightM = $profile->height_cm / 100;
            $targetBmi = $profile->target_weight_kg / ($heightM * $heightM);
            if ($targetBmi < 18) {
                $risks[] = [
                    'type' => 'danger',
                    'message' => 'Sua meta de peso resultaria em um IMC abaixo do saudável. Recomendamos revisão profissional.'
                ];
            }
        }

        // Inconsistência de calorias
        if ($profile->daily_calorie_target < 1200 && $profile->sex === 'F') {
            $risks[] = [
                'type' => 'warning',
                'message' => 'Sua meta calórica está muito baixa para uma mulher adulta. Risco de queda metabólica.'
            ];
        }

        if ($profile->daily_calorie_target < 1500 && $profile->sex === 'M') {
            $risks[] = [
                'type' => 'warning',
                'message' => 'Sua meta calórica está muito baixa para um homem adulto.'
            ];
        }

        return $risks;
    }
}
