<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\LoadLog;
use App\Support\PatientAccessGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoadLogController extends Controller
{
    use FormatsApiResponses;

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'training_plan_exercise_id' => ['required', 'integer', 'exists:training_plan_exercises,id'],
            'exercise_id' => ['required', 'integer', 'exists:exercises_catalog,id'],
            'log_date' => ['required', 'date'],
            'set_number' => ['required', 'integer', 'min:1', 'max:50'],
            'reps_done' => ['required', 'integer', 'min:0', 'max:500'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:2000'],
            'rpe' => ['nullable', 'integer', 'min:1', 'max:10'],
            'to_failure' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $planExercise = PatientAccessGuard::assertTrainingPlanExerciseAccess(
            $request->user(),
            (int) $data['training_plan_exercise_id']
        );

        if ((int) $planExercise->exercise_id !== (int) $data['exercise_id']) {
            return $this->error('Exercício não pertence a este item do plano.', 422, 'invalid_exercise');
        }

        $log = LoadLog::create([
            'user_id' => $request->user()->id,
            'training_plan_exercise_id' => $data['training_plan_exercise_id'],
            'exercise_id' => $data['exercise_id'],
            'log_date' => $data['log_date'],
            'set_number' => $data['set_number'],
            'reps_done' => $data['reps_done'],
            'to_failure' => (bool) ($data['to_failure'] ?? false),
            'weight_kg' => $data['weight_kg'] ?? null,
            'rpe' => $data['rpe'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return $this->success($this->payload($log), status: 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(LoadLog $log): array
    {
        return [
            'id' => $log->id,
            'training_plan_exercise_id' => $log->training_plan_exercise_id,
            'exercise_id' => $log->exercise_id,
            'log_date' => $log->log_date?->toDateString(),
            'set_number' => $log->set_number,
            'reps_done' => $log->reps_done,
            'weight_kg' => $log->weight_kg !== null ? (float) $log->weight_kg : null,
            'rpe' => $log->rpe,
            'to_failure' => (bool) $log->to_failure,
            'one_rm' => $log->one_rm !== null ? (float) $log->one_rm : null,
        ];
    }
}
