<?php

namespace App\Services;

use DateTimeImmutable;
use Exception;

class Nutrition
{
    public static function ageYears(string $birthYmd, ?DateTimeImmutable $ref = null): ?int
    {
        try {
            $birth = new DateTimeImmutable($birthYmd);
        } catch (Exception) {
            return null;
        }
        $ref = $ref ?? new DateTimeImmutable('today');
        if ($birth > $ref) {
            return null;
        }

        return (int) $birth->diff($ref)->y;
    }

    public static function activityFactor(string $level): float
    {
        return match ($level) {
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9,
            default => 1.55,
        };
    }

    public static function bmr(float $weightKg, int $heightCm, int $ageYears, string $sex): float
    {
        $base = (10.0 * $weightKg) + (6.25 * $heightCm) - (5.0 * $ageYears);

        return match ($sex) {
            'F' => $base - 161.0,
            'M' => $base + 5.0,
            default => $base - 78.0,
        };
    }

    public static function tdee(float $bmr, string $activityLevel): float
    {
        return $bmr * self::activityFactor($activityLevel);
    }

    public static function dailyTargetKcal(float $tdee, string $goal, string $sex): int
    {
        $raw = match ($goal) {
            'lose' => $tdee - 500.0,
            'gain' => $tdee + 300.0,
            default => $tdee,
        };
        $minSafe = match ($sex) {
            'F' => 1200.0,
            'M' => 1500.0,
            default => 1350.0,
        };
        if ($goal === 'lose') {
            $raw = max($raw, $minSafe);
        }
        $rounded = (int) round($raw);

        return max(500, min(20000, $rounded));
    }

    /**
     * @return array{ok: true, target: int, bmr: float, tdee: float, weight_kg: float, age: int}|array{ok: false, message: string}
     */
    public static function estimateTarget(
        ?string $birthDate,
        ?int $heightCm,
        string $sex,
        string $activityLevel,
        string $goal,
        ?float $latestWeightKg,
    ): array {
        if ($latestWeightKg === null || $latestWeightKg < 20.0 || $latestWeightKg > 400.0) {
            return ['ok' => false, 'message' => 'É necessário um peso recente válido. Registre em Peso.'];
        }
        if ($birthDate === null || $heightCm === null || $heightCm < 50 || $heightCm > 260) {
            return ['ok' => false, 'message' => 'Preencha data de nascimento e altura para calcular a meta.'];
        }
        $age = self::ageYears($birthDate);
        if ($age === null || $age < 14 || $age > 99) {
            return [
                'ok' => false,
                'message' => 'Esta estimativa usa faixa etária de 14–99 anos. Verifique a data de nascimento.',
            ];
        }
        $bmr = self::bmr($latestWeightKg, $heightCm, $age, $sex);
        if ($bmr < 500.0) {
            return ['ok' => false, 'message' => 'Valores inconsistentes para TMB. Confira peso, altura e idade.'];
        }
        $tdee = self::tdee($bmr, $activityLevel);
        $target = self::dailyTargetKcal($tdee, $goal, $sex);

        return [
            'ok' => true,
            'target' => $target,
            'bmr' => $bmr,
            'tdee' => $tdee,
            'weight_kg' => $latestWeightKg,
            'age' => $age,
        ];
    }

    /**
     * @return array{p: float, c: float, f: float}
     */
    public static function defaultMacroTargetsFromKcal(int $dailyKcal): array
    {
        if ($dailyKcal < 1) {
            return ['p' => 0.0, 'c' => 0.0, 'f' => 0.0];
        }
        $pKcal = 0.25 * $dailyKcal;
        $cKcal = 0.45 * $dailyKcal;
        $fKcal = 0.30 * $dailyKcal;

        return [
            'p' => round($pKcal / 4.0, 1),
            'c' => round($cKcal / 4.0, 1),
            'f' => round($fKcal / 9.0, 1),
        ];
    }

    /**
     * @param  array<string, mixed>  $profRow
     * @return array{p: ?float, c: ?float, f: ?float}
     */
    public static function macroTargetsForDisplay(bool $isPremium, array $profRow): array
    {
        if ($isPremium) {
            return [
                'p' => isset($profRow['protein_target_g']) && $profRow['protein_target_g'] !== null
                    ? (float) $profRow['protein_target_g'] : null,
                'c' => isset($profRow['carbs_target_g']) && $profRow['carbs_target_g'] !== null
                    ? (float) $profRow['carbs_target_g'] : null,
                'f' => isset($profRow['fat_target_g']) && $profRow['fat_target_g'] !== null
                    ? (float) $profRow['fat_target_g'] : null,
            ];
        }
        $kcal = isset($profRow['daily_calorie_target']) && $profRow['daily_calorie_target'] !== null
            ? (int) $profRow['daily_calorie_target'] : null;
        if ($kcal === null || $kcal < 1) {
            return ['p' => null, 'c' => null, 'f' => null];
        }
        $d = self::defaultMacroTargetsFromKcal($kcal);

        return ['p' => $d['p'], 'c' => $d['c'], 'f' => $d['f']];
    }

    public static function calculateWaterTarget(
        float $weightKg,
        ?string $birthDate,
        string $sex,
        string $activityLevel,
        string $climate
    ): int {
        // Base: 35ml per kg is the standard for adults
        $mlPerKg = 35;

        $age = $birthDate ? self::ageYears($birthDate) : null;

        if ($age !== null) {
            if ($age < 30) {
                $mlPerKg = 40;
            } elseif ($age <= 55) {
                $mlPerKg = 35;
            } elseif ($age <= 65) {
                $mlPerKg = 30;
            } else {
                $mlPerKg = 25;
            }
        }

        $base = $weightKg * $mlPerKg;

        // Activity adjustment: moderate (+500ml), active/very active (+1000ml)
        $activityBonus = match ($activityLevel) {
            'moderate' => 500,
            'active', 'very_active' => 1000,
            default => 0
        };

        // Climate adjustment: hot (+500ml), moderate (+250ml)
        $climateBonus = match ($climate) {
            'hot' => 500,
            'moderate' => 250,
            default => 0
        };

        // Sex adjustment (minor difference in baseline metabolism)
        $sexBonus = ($sex === 'M') ? 200 : 0;

        $total = $base + $activityBonus + $climateBonus + $sexBonus;

        // Round to nearest 50ml and keep within safe bounds (500ml to 10L)
        $rounded = (int) (round($total / 50) * 50);

        return max(500, min(10000, $rounded));
    }
}
