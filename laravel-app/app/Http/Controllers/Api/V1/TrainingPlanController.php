<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsTrainingPlans;
use App\Http\Controllers\Controller;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingPlanController extends Controller
{
    use FormatsTrainingPlans;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', TrainingPlan::class);

        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();

        $query = TrainingPlan::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('creator_id', $user->id);
            });

        $activeProfId = session('active_professional_id') ?: $request->attributes->get('active_professional_id');
        $activeClinicId = session('active_clinic_id') ?: $request->attributes->get('active_clinic_id');

        if ($activeProfId) {
            $query->where('professional_id', $activeProfId);
        } elseif ($activeClinicId) {
            $query->where('academy_company_id', $activeClinicId);
        } elseif ($request->header('X-Active-Context-ID') === 'personal' || session('active_personal_context')) {
            $query->whereNull('professional_id');
        }

        $query->withCount('exercises')->latest();

        if (! $isPremium) {
            $query->limit(3);
        }

        $plans = $query->get();

        $primaryLink = \App\Models\ProfessionalPatient::where('user_id', $user->id)
            ->where('status', 'Sim')
            ->first();

        $hasProfessionalLink = $primaryLink !== null;
        $canCreateOwnWorkout = $hasProfessionalLink
            ? (bool) $primaryLink->hasPermission('can_create_own_workout')
            : (bool) $user->hasFeature('create_workout');

        return response()->json([
            'data' => $plans->map(fn (TrainingPlan $plan) => $this->planSummary($plan)),
            'meta' => [
                'is_premium' => $isPremium,
                'count' => $plans->count(),
                'has_professional_link' => $hasProfessionalLink,
                'can_create_own_workout' => $canCreateOwnWorkout,
            ],
        ]);
    }

    public function show(Request $request, TrainingPlan $trainingPlan): JsonResponse
    {
        $this->authorize('view', $trainingPlan);

        $user = $request->user();
        if ($user->isResourceOverLimit('workouts', $trainingPlan->id)) {
            return response()->json([
                'message' => 'Plano bloqueado pelo limite do plano atual.',
            ], 403);
        }

        $trainingPlan->load(['exercises.catalogExercise', 'exercises.sets']);

        return response()->json([
            'data' => $this->planDetail($trainingPlan),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', TrainingPlan::class);

        $user = $request->user();

        $validated = $this->validatePlanPayload($request);

        $maxWorkouts = $user->getPlanLimit('max_workouts');
        if ($maxWorkouts > 0) {
            $planCount = TrainingPlan::where('user_id', $user->id)->count();
            if ($planCount >= $maxWorkouts) {
                return response()->json([
                    'message' => "Você atingiu o limite de {$maxWorkouts} planos de treino.",
                ], 403);
            }
        }

        $plan = TrainingPlan::create(array_merge([
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'professional_id' => null,
        ], $this->planAttributes($validated)));

        $this->replaceExercises($plan, $validated['exercises'] ?? []);

        return response()->json([
            'message' => 'Plano de treino criado com sucesso.',
            'data' => $this->planSummary($plan->loadCount('exercises')),
        ], 201);
    }

    public function update(Request $request, TrainingPlan $trainingPlan): JsonResponse
    {
        $this->authorize('update', $trainingPlan);

        if ($request->user()->isResourceOverLimit('workouts', $trainingPlan->id)) {
            return response()->json([
                'message' => 'Plano bloqueado pelo limite do plano atual.',
            ], 403);
        }

        $validated = $this->validatePlanPayload($request);
        $trainingPlan->update($this->planAttributes($validated));
        $this->replaceExercises($trainingPlan, $validated['exercises'] ?? []);

        return response()->json([
            'message' => 'Plano de treino atualizado com sucesso.',
            'data' => $this->planSummary($trainingPlan->loadCount('exercises')),
        ]);
    }

    public function destroy(Request $request, TrainingPlan $trainingPlan): JsonResponse
    {
        $this->authorize('delete', $trainingPlan);

        $trainingPlan->delete();

        return response()->json([
            'message' => 'Plano de treino excluÃ­do com sucesso.',
            'data' => new \stdClass(),
        ]);
    }

    private function validatePlanPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'plan_label' => ['nullable', 'string', 'max:10'],
            'goal' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'frequency' => ['nullable', 'integer', 'min:1', 'max:7'],
            'difficulty' => ['nullable', 'string', 'max:20'],
            'estimated_duration' => ['nullable', 'integer', 'min:1'],
            'student_profile' => ['nullable', 'string', 'max:30'],
            'split_type' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'string', 'max:20'],
            'days_of_week' => ['nullable', 'array'],
            'total_volume' => ['nullable', 'numeric'],
            'muscles_worked' => ['nullable', 'array'],
            'is_template' => ['nullable', 'boolean'],
            'exercises' => ['nullable', 'array'],
            'exercises.*.id' => ['required_with:exercises', 'integer', 'exists:exercises_catalog,id'],
            'exercises.*.notes' => ['nullable', 'string', 'max:1000'],
            'exercises.*.sets' => ['required_with:exercises', 'array', 'min:1'],
            'exercises.*.sets.*.type' => ['nullable', 'string', 'max:20'],
            'exercises.*.sets.*.reps' => ['nullable', 'integer', 'min:0', 'max:999'],
            'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'exercises.*.sets.*.rest' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'exercises.*.sets.*.rpe' => ['nullable', 'integer', 'min:1', 'max:10'],
            'exercises.*.sets.*.cadence' => ['nullable', 'string', 'max:20'],
        ]);
    }

    private function planAttributes(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'plan_label' => $validated['plan_label'] ?? null,
            'goal' => $validated['goal'] ?? null,
            'description' => $validated['description'] ?? null,
            'frequency' => $validated['frequency'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'student_profile' => $validated['student_profile'] ?? null,
            'split_type' => $validated['split_type'] ?? null,
            'days_of_week' => $validated['days_of_week'] ?? null,
            'total_volume' => $validated['total_volume'] ?? 0,
            'muscles_worked' => $validated['muscles_worked'] ?? null,
            'is_template' => (bool) ($validated['is_template'] ?? false),
            'status' => $validated['status'] ?? 'Ativo',
            'is_active' => true,
        ];
    }

    private function replaceExercises(TrainingPlan $plan, array $exercises): void
    {
        $plan->exercises()->delete();

        foreach ($exercises as $index => $exerciseData) {
            $planExercise = TrainingPlanExercise::create([
                'training_plan_id' => $plan->id,
                'exercise_id' => $exerciseData['id'],
                'position' => $index,
                'notes' => $exerciseData['notes'] ?? null,
            ]);

            foreach ($exerciseData['sets'] as $setIndex => $setData) {
                $planExercise->sets()->create([
                    'set_number' => $setIndex + 1,
                    'reps_target' => $setData['reps'] ?? 0,
                    'weight_target' => $setData['weight'] ?? 0,
                    'rest_seconds' => $setData['rest'] ?? 60,
                    'rpe_target' => $setData['rpe'] ?? null,
                    'cadence' => $setData['cadence'] ?? null,
                    'set_type' => $setData['type'] ?? 'work',
                ]);
            }
        }
    }
}
