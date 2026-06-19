<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Models\ExerciseCatalog;
use App\Models\ExerciseSet;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;

trait FormatsTrainingPlans
{
    /**
     * @return array<string, mixed>
     */
    protected function planSummary(TrainingPlan $plan): array
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
    protected function planDetail(TrainingPlan $plan): array
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
