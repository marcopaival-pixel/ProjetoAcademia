<?php
declare(strict_types=1);

/**
 * Estimativas energéticas para sugerir meta diária (referência geral, não substitui avaliação profissional).
 */

function nutrition_age_years(string $birthYmd, ?DateTimeImmutable $ref = null): ?int
{
    try {
        $birth = new DateTimeImmutable($birthYmd);
    } catch (Exception $e) {
        return null;
    }
    $ref = $ref ?? new DateTimeImmutable('today');
    if ($birth > $ref) {
        return null;
    }
    return (int) $birth->diff($ref)->y;
}

function nutrition_activity_factor(string $level): float
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

/** BMR — Mifflin–St Jeor (kcal/dia). */
function nutrition_bmr(float $weightKg, int $heightCm, int $ageYears, string $sex): float
{
    $base = (10.0 * $weightKg) + (6.25 * $heightCm) - (5.0 * $ageYears);
    return match ($sex) {
        'F' => $base - 161.0,
        'M' => $base + 5.0,
        default => $base - 78.0,
    };
}

function nutrition_tdee(float $bmr, string $activityLevel): float
{
    return $bmr * nutrition_activity_factor($activityLevel);
}

/** Déficit/superávit simples + piso ao emagrecer (orientação geral). */
function nutrition_daily_target_kcal(float $tdee, string $goal, string $sex): int
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
 * @return array{
 *   ok: true,
 *   target: int,
 *   bmr: float,
 *   tdee: float,
 *   weight_kg: float,
 *   age: int
 * }|array{ok: false, message: string}
 */
function nutrition_estimate_target(
    ?string $birthDate,
    ?int $heightCm,
    string $sex,
    string $activityLevel,
    string $goal,
    ?float $latestWeightKg
): array {
    if ($latestWeightKg === null || $latestWeightKg < 20.0 || $latestWeightKg > 400.0) {
        return ['ok' => false, 'message' => 'É necessário um peso recente válido. Registre em Peso.'];
    }
    if ($birthDate === null || $heightCm === null || $heightCm < 50 || $heightCm > 260) {
        return ['ok' => false, 'message' => 'Preencha data de nascimento e altura para calcular a meta.'];
    }
    $age = nutrition_age_years($birthDate);
    if ($age === null || $age < 14 || $age > 99) {
        return [
            'ok' => false,
            'message' => 'Esta estimativa usa faixa etária de 14–99 anos. Verifique a data de nascimento.',
        ];
    }
    $bmr = nutrition_bmr($latestWeightKg, $heightCm, $age, $sex);
    if ($bmr < 500.0) {
        return ['ok' => false, 'message' => 'Valores inconsistentes para TMB. Confira peso, altura e idade.'];
    }
    $tdee = nutrition_tdee($bmr, $activityLevel);
    $target = nutrition_daily_target_kcal($tdee, $goal, $sex);
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
 * Metas de macro derivadas só da meta calórica (plano grátis): ~25% P, 45% C, 30% G das kcal.
 *
 * @return array{p: float, c: float, f: float}
 */
function nutrition_default_macro_targets_from_kcal(int $dailyKcal): array
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
 * Metas exibidas em Hoje / Diário: Premium usa o perfil; grátis usa repartição padrão a partir das kcal.
 *
 * @param array<string, mixed> $profRow daily_calorie_target, protein_target_g, carbs_target_g, fat_target_g
 * @return array{p: ?float, c: ?float, f: ?float}
 */
function nutrition_macro_targets_for_display(bool $isPremium, array $profRow): array
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
    $d = nutrition_default_macro_targets_from_kcal($kcal);
    return ['p' => $d['p'], 'c' => $d['c'], 'f' => $d['f']];
}
