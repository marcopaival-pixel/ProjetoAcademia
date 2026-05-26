<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ExerciseCatalog;
use App\Models\ExerciseSet;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', TrainingPlan::class);

        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();

        $query = TrainingPlan::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('creator_id', $user->id);
            })
            ->withCount('exercises')
            ->latest();

        if (! $isPremium) {
            $query->limit(3);
        }

        $plans = $query->get();

        return response()->json([
            'data' => $plans->map(fn (TrainingPlan $plan) => $this->planSummary($plan)),
            'meta' => [
                'is_premium' => $isPremium,
                'count' => $plans->count(),
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

    /**
     * @return array<string, mixed>
     */
    private function planSummary(TrainingPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'plan_label' => $plan->plan_label,
            'goal' => $plan->goal,
            'status' => $plan->status,
            'is_active' => (bool) $plan->is_active,
            'exercises_count' => $plan->exercises_count ?? $plan->exercises()->count(),
            'created_at' => $plan->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function planDetail(TrainingPlan $plan): array
    {
        return array_merge($this->planSummary($plan), [
            'description' => $plan->description,
            'frequency' => $plan->frequency,
            'difficulty' => $plan->difficulty,
            'days_of_week' => $plan->days_of_week,
            'exercises' => $plan->exercises->map(function (TrainingPlanExercise $exercise): array {
                /** @var ExerciseCatalog|null $catalog */
                $catalog = $exercise->catalogExercise;

                return [
                    'id' => $exercise->id,
                    'position' => $exercise->position,
                    'name' => $exercise->custom_name ?? $catalog?->name,
                    'muscle_group' => $catalog?->muscle_group,
                    'notes' => $exercise->notes,
                    'sets' => $exercise->sets->map(fn (ExerciseSet $set): array => [
                        'id' => $set->id,
                        'set_number' => $set->set_number,
                        'reps_target' => $set->reps_target,
                        'rest_seconds' => $set->rest_seconds,
                        'rpe_target' => $set->rpe_target,
                        'set_type' => $set->set_type,
                    ])->values()->all(),
                ];
            })->values()->all(),
        ]);
    }
}
