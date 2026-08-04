<?php

namespace App\Services;

use App\Models\AIChat;
use App\Models\BodyAssessment;
use App\Models\FoodEntry;
use App\Models\SmartStack;
use App\Models\Supplement;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WaterEntry;
use App\Models\WeightEntry;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Schema;

class StudentContextService
{
    /**
     * Metricas consolidadas para user_metrics no orquestrador / chat.
     *
     * @return array<string, scalar|null>
     */
    public function metrics(User $user): array
    {
        $profile = $this->profile($user);
        $latestWeight = $this->latestWeightKg($user);
        $isPremium = $this->userHasPremiumAccess($user);
        $today = now()->toDateString();

        $estimate = Nutrition::estimateTarget(
            $profile->birth_date ? (string) $profile->birth_date : null,
            (int) ($profile->height_cm ?? 0),
            $profile->sex ?? 'M',
            $profile->activity_level ?? 'moderate',
            $profile->goal ?? 'maintain',
            $latestWeight,
        );

        $dailyTarget = $estimate['ok']
            ? $estimate['target']
            : ($profile->daily_calorie_target ?? 2000);

        $macroTargets = Nutrition::macroTargetsForDisplay($isPremium, $profile->toArray());
        $todaySums = $this->todayFoodTotals($user->id, $today);

        $waterTarget = $profile->water_target_ml;
        if (! $waterTarget && $latestWeight !== null) {
            $waterTarget = Nutrition::calculateWaterTarget(
                $latestWeight,
                $profile->birth_date ? (string) $profile->birth_date : null,
                $profile->sex ?? 'M',
                $profile->activity_level ?? 'moderate',
                $profile->climate ?? 'temperate',
            );
        }

        $lastWorkout = Schema::hasTable('workout_sessions')
            ? WorkoutSession::query()
                ->withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->with('trainingPlan')
                ->latest()
                ->first()
            : null;

        $activePlan = Schema::hasTable('training_plans')
            ? TrainingPlan::query()
                ->withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->first()
            : null;

        $assessment = Schema::hasTable('body_assessments')
            ? BodyAssessment::query()
                ->withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->latest('assessment_date')
                ->first()
            : null;

        $remaining = [
            'kcal' => max($dailyTarget - (int) ($todaySums->cal ?? 0), 0),
            'protein_g' => max((float) ($macroTargets['p'] ?? 0) - (float) ($todaySums->p ?? 0), 0),
            'carbs_g' => max((float) ($macroTargets['c'] ?? 0) - (float) ($todaySums->c ?? 0), 0),
            'fat_g' => max((float) ($macroTargets['f'] ?? 0) - (float) ($todaySums->f ?? 0), 0),
        ];

        return array_filter([
            'name' => $user->name,
            'objective' => $profile->goal ?? 'maintain',
            'current_weight_kg' => $latestWeight,
            'goal_weight_kg' => $profile->target_weight_kg,
            'height_cm' => $profile->height_cm,
            'biological_sex' => $profile->sex ?? 'nao informado',
            'activity_level' => $profile->activity_level ?? 'moderate',
            'physical_level' => $profile->physical_level,
            'experience_level' => $profile->experience_level,
            'training_location' => $profile->training_location,
            'training_days_per_week' => $profile->training_days_per_week,
            'sleep_hours' => $profile->sleep_hours,
            'daily_calories_target' => $dailyTarget,
            'consumed_calories_today' => (int) ($todaySums->cal ?? 0),
            'protein_target_g' => $macroTargets['p'] ?? null,
            'carbs_target_g' => $macroTargets['c'] ?? null,
            'fat_target_g' => $macroTargets['f'] ?? null,
            'remaining_calories_today' => $remaining['kcal'],
            'remaining_protein_g' => round($remaining['protein_g'], 1),
            'remaining_carbs_g' => round($remaining['carbs_g'], 1),
            'remaining_fat_g' => round($remaining['fat_g'], 1),
            'water_target_ml' => $waterTarget,
            'water_consumed_ml' => Schema::hasTable('water_entries')
                ? (int) WaterEntry::query()
                    ->withoutGlobalScopes()
                    ->where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->sum('amount_ml')
                : 0,
            'last_workout_name' => $lastWorkout?->trainingPlan?->name ?? 'Nenhum treino registrado recentemente',
            'last_workout_date' => $lastWorkout?->created_at?->diffForHumans() ?? 'N/A',
            'active_training_plan' => $activePlan?->name,
            'active_plan_frequency' => $activePlan?->frequency,
            'has_injury' => $profile->has_injury ? 'sim' : 'nao',
            'injury_details' => $profile->has_injury ? $profile->injury_details : null,
            'has_disease' => $profile->has_disease ? 'sim' : 'nao',
            'disease_details' => $profile->has_disease ? $profile->disease_details : null,
            'has_allergy' => $profile->has_allergy ? 'sim' : 'nao',
            'allergy_details' => $profile->has_allergy ? $profile->allergy_details : null,
            'uses_medication' => $profile->uses_medication ? 'sim' : 'nao',
            'medication_details' => $profile->uses_medication ? $profile->medication_details : null,
            'fitness_notes' => $profile->fitness_notes,
            'latest_assessment_date' => $assessment?->assessment_date,
            'latest_body_fat_percent' => $assessment?->bf_percent,
            'latest_muscle_mass_kg' => $assessment?->muscle_mass_kg,
        ], fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    public function conversationHistory(int $userId, int $limit = 16): array
    {
        if (! Schema::hasTable('ai_chats')) {
            return [];
        }

        return AIChat::query()
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn (AIChat $chat) => [
                'role' => $chat->role,
                'content' => mb_substr($chat->message, 0, 1200),
            ])
            ->values()
            ->all();
    }

    /**
     * Bloco de texto para injetar no system prompt dos agentes de dominio.
     */
    public function promptBlock(User $user, string $focus = 'general'): string
    {
        $metrics = $this->metrics($user);
        $lines = [
            'PERFIL CONSOLIDADO DO ALUNO (dados reais do NexShape — use apenas estas informacoes; se faltar algo, oriente a registrar no perfil, nao invente valores):',
        ];

        foreach ($this->linesForFocus($metrics, $focus) as $line) {
            $lines[] = '- '.$line;
        }

        $activeSupplements = $this->activeSupplementsSummary($user);
        if ($activeSupplements !== '' && in_array($focus, ['supplements', 'nutrition'], true)) {
            $lines[] = '- Suplementos/stacks ativos: '.$activeSupplements;
        }

        return implode("\n", $lines);
    }

    /**
     * Lista resumida de suplementos em stacks ativos do aluno.
     */
    public function activeSupplementsSummary(User $user): string
    {
        if (! Schema::hasTable('smart_stacks') || ! Schema::hasTable('supplements')) {
            return '';
        }

        $items = SmartStack::query()
            ->withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('status', 'ativo')
            ->with(['supplements' => function ($query) {
                $query->withoutGlobalScopes()->where('is_active', true);
            }])
            ->get()
            ->flatMap(function (SmartStack $stack) {
                return $stack->supplements->map(function (Supplement $supplement) {
                    $label = $supplement->name;
                    if ($supplement->dosage) {
                        $label .= ' ('.$supplement->dosage.($supplement->unit ? ' '.$supplement->unit : '').')';
                    }

                    return $label;
                });
            })
            ->unique()
            ->values();

        return $items->isEmpty() ? '' : $items->implode('; ');
    }

    /**
     * Infere intencao do orquestrador a partir de modulo/categoria da consulta.
     */
    public function resolveQueryIntent(string $modulo, string $categoria): ?string
    {
        $haystack = mb_strtolower(trim($modulo.' '.$categoria));

        if ($haystack === '' || $haystack === 'geral geral') {
            return null;
        }

        if (preg_match('/nutri|dieta|aliment|refeic|macro|suplement/', $haystack)) {
            return 'nutrition';
        }

        if (preg_match('/trein|workout|exerc|muscul|hipertrof/', $haystack)) {
            return 'training';
        }

        if (preg_match('/clinic|saude|exame|bioimped|corporal/', $haystack)) {
            return 'clinical';
        }

        return null;
    }

    /**
     * @param  array<string, scalar|null>  $metrics
     * @return list<string>
     */
    private function linesForFocus(array $metrics, string $focus): array
    {
        $keys = match ($focus) {
            'training' => [
                'name', 'objective', 'physical_level', 'experience_level', 'training_location',
                'training_days_per_week', 'sleep_hours', 'active_training_plan', 'active_plan_frequency',
                'last_workout_name', 'last_workout_date', 'has_injury', 'injury_details',
                'has_disease', 'disease_details', 'fitness_notes',
            ],
            'nutrition' => [
                'name', 'objective', 'current_weight_kg', 'goal_weight_kg', 'height_cm',
                'daily_calories_target', 'consumed_calories_today', 'remaining_calories_today',
                'protein_target_g', 'remaining_protein_g', 'carbs_target_g', 'remaining_carbs_g',
                'fat_target_g', 'remaining_fat_g', 'water_target_ml', 'water_consumed_ml',
                'has_allergy', 'allergy_details', 'uses_medication', 'medication_details',
                'has_disease', 'disease_details', 'activity_level', 'training_days_per_week',
            ],
            'supplements' => [
                'name', 'objective', 'current_weight_kg', 'goal_weight_kg',
                'has_allergy', 'allergy_details', 'uses_medication', 'medication_details',
                'has_disease', 'disease_details', 'activity_level', 'training_days_per_week',
                'active_training_plan', 'last_workout_name',
            ],
            default => array_keys($metrics),
        };

        $lines = [];
        foreach ($keys as $key) {
            if (! array_key_exists($key, $metrics)) {
                continue;
            }

            $label = str_replace('_', ' ', $key);
            $lines[] = ucfirst($label).': '.$metrics[$key];
        }

        return $lines;
    }

    private function profile(User $user): UserProfile
    {
        if ($user->relationLoaded('profile') && $user->profile) {
            return $user->profile;
        }

        if (! Schema::hasTable('user_profiles')) {
            return new UserProfile([
                'user_id' => $user->id,
                'goal' => 'maintain',
                'activity_level' => 'moderate',
                'sex' => 'M',
            ]);
        }

        return UserProfile::firstOrCreate(['user_id' => $user->id]);
    }

    private function latestWeightKg(User $user): ?float
    {
        if (! Schema::hasTable('weight_entries')) {
            return null;
        }

        $weight = WeightEntry::query()
            ->withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->orderByDesc('weighed_at')
            ->orderByDesc('id')
            ->value('weight_kg');

        return $weight !== null ? (float) $weight : null;
    }

    private function todayFoodTotals(int $userId, string $date): object
    {
        if (! Schema::hasTable('food_entries')) {
            return (object) ['cal' => 0, 'p' => 0, 'c' => 0, 'f' => 0];
        }

        return FoodEntry::query()
            ->withoutGlobalScopes()
            ->where('user_id', $userId)
            ->whereDate('entry_date', $date)
            ->selectRaw('SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f')
            ->first() ?? (object) ['cal' => 0, 'p' => 0, 'c' => 0, 'f' => 0];
    }

    private function userHasPremiumAccess(User $user): bool
    {
        if ($user->is_admin ?? false) {
            return true;
        }

        if ($user->is_premium ?? false) {
            return true;
        }

        if (! Schema::hasTable('subscriptions')) {
            return false;
        }

        return $user->hasPremiumAccess();
    }
}
