<?php

namespace App\Services;

use App\Models\BodyAssessment;
use App\Models\ExerciseCatalog;
use App\Models\ExerciseSet;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\User;
use App\Services\AI\AIProviderService;
use App\Services\AI\PrescriptionSafetyAuditor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIFitnessGeneratorService
{
    public function __construct(
        private AIProviderService $aiProvider,
        private PrescriptionSafetyAuditor $safetyAuditor
    ) {}

    public function generateTrainingPlan(User $user): ?TrainingPlan
    {
        $profile = $user->profile;
        if (! $profile) {
            return null;
        }

        $result = $this->aiProvider->call($user, [
            ['role' => 'system', 'content' => 'Voce e um especialista em treino. Retorne apenas JSON valido e conservador.'],
            ['role' => 'user', 'content' => $this->buildTrainingPrompt($user)],
        ], 'fitness_training_generator', 'main', [
            'temperature' => 0.3,
            'response_format' => ['type' => 'json_object'],
            'feature_key' => 'fitness_training_generation',
        ]);

        if (! ($result['ok'] ?? false)) {
            Log::warning('AI training generation failed', ['error' => $result['error'] ?? null]);
            return null;
        }

        $audit = $this->safetyAuditor->audit($user, 'training', $result['message'], ['feature_key' => 'fitness_training_generation']);
        if (! ($audit['approved'] ?? false)) {
            Log::warning('AI training generation blocked by audit', ['audit' => $audit]);
            return null;
        }

        $planData = json_decode((string) $result['message'], true);
        if (! is_array($planData)) {
            return null;
        }

        return DB::transaction(function () use ($user, $planData) {
            $plan = TrainingPlan::create([
                'user_id' => $user->id,
                'name' => $planData['name'] ?? 'Treino Gerado por IA',
                'goal' => $planData['goal'] ?? $user->profile->goal,
                'description' => $planData['notes'] ?? 'Plano gerado automaticamente pelo NexShape.',
                'is_active' => true,
                'created_by_ai' => true,
                'status' => 'active',
            ]);

            foreach (($planData['exercises'] ?? []) as $index => $ex) {
                $catalogEx = ExerciseCatalog::where('name', 'like', '%' . ($ex['name'] ?? '') . '%')->first();

                $trainingExercise = TrainingPlanExercise::create([
                    'training_plan_id' => $plan->id,
                    'exercise_id' => $catalogEx?->id,
                    'custom_name' => $catalogEx ? null : ($ex['name'] ?? 'Exercicio sugerido'),
                    'position' => $index + 1,
                    'notes' => $ex['notes'] ?? null,
                ]);

                for ($i = 1, $sets = max(1, (int) ($ex['sets'] ?? 3)); $i <= $sets; $i++) {
                    ExerciseSet::create([
                        'training_plan_exercise_id' => $trainingExercise->id,
                        'set_number' => $i,
                        'reps_target' => $ex['reps'] ?? '10-12',
                        'rest_seconds' => $ex['rest_seconds'] ?? 60,
                        'rpe_target' => $ex['rpe'] ?? 8,
                        'set_type' => 'normal',
                    ]);
                }
            }

            return $plan;
        });
    }

    public function generateMealPlan(User $user): array
    {
        $profile = $user->profile;
        if (! $profile) {
            return ['ok' => false, 'error' => 'Perfil nao encontrado'];
        }

        $result = $this->aiProvider->call($user, [
            ['role' => 'system', 'content' => 'Voce e um nutricionista esportivo. Retorne apenas JSON valido, sem dietas extremas.'],
            ['role' => 'user', 'content' => $this->buildMealPrompt($user)],
        ], 'fitness_meal_generator', 'main', [
            'temperature' => 0.3,
            'response_format' => ['type' => 'json_object'],
            'feature_key' => 'fitness_meal_generation',
        ]);

        if (! ($result['ok'] ?? false)) {
            return ['ok' => false, 'error' => $result['error'] ?? 'IA indisponivel'];
        }

        $audit = $this->safetyAuditor->audit($user, 'nutrition', $result['message'], ['feature_key' => 'fitness_meal_generation']);
        if (! ($audit['approved'] ?? false)) {
            return ['ok' => false, 'error' => 'Plano bloqueado pela auditoria de seguranca.', 'audit' => $audit];
        }

        return ['ok' => true, 'plan' => json_decode((string) $result['message'], true) ?: []];
    }

    public function generateEvolutionReport(User $user): array
    {
        $assessment = BodyAssessment::where('user_id', $user->id)->latest('assessment_date')->first();

        $result = $this->aiProvider->call($user, [
            ['role' => 'system', 'content' => 'Voce gera relatorios de evolucao conservadores. Nunca invente dados. Retorne JSON valido.'],
            ['role' => 'user', 'content' => json_encode([
                'name' => $user->name,
                'profile' => $user->profile?->only(['goal', 'height_cm', 'activity_level']),
                'latest_assessment' => $assessment?->toArray(),
            ], JSON_UNESCAPED_UNICODE)],
        ], 'fitness_evolution_report', 'main', [
            'temperature' => 0.2,
            'response_format' => ['type' => 'json_object'],
            'feature_key' => 'evolution_report',
        ]);

        if (! ($result['ok'] ?? false)) {
            return ['ok' => false, 'error' => $result['error'] ?? 'IA indisponivel'];
        }

        return ['ok' => true, 'report' => $result['message']];
    }

    private function buildTrainingPrompt(User $user): string
    {
        $profile = $user->profile;

        return json_encode([
            'task' => 'Gere um plano de treino semanal com name, goal, notes e exercises.',
            'required_exercise_fields' => ['name', 'sets', 'reps', 'rest_seconds', 'rpe', 'notes'],
            'user' => [
                'name' => $user->name,
                'goal' => $profile->goal ?? null,
                'physical_level' => $profile->physical_level ?? $profile->fitness_level ?? null,
                'training_location' => $profile->training_location ?? null,
                'available_daily_time_mins' => $profile->available_daily_time_mins ?? null,
                'training_days_per_week' => $profile->training_days_per_week ?? null,
                'restrictions' => $profile->fitness_notes ?? null,
            ],
        ], JSON_UNESCAPED_UNICODE);
    }

    private function buildMealPrompt(User $user): string
    {
        $profile = $user->profile;

        return json_encode([
            'task' => 'Gere sugestao alimentar diaria com daily_summary e meals.',
            'user' => [
                'goal' => $profile->goal ?? null,
                'daily_calorie_target' => $profile->daily_calorie_target ?? null,
                'activity_level' => $profile->activity_level ?? null,
                'restrictions' => $profile->fitness_notes ?? null,
            ],
        ], JSON_UNESCAPED_UNICODE);
    }
}
