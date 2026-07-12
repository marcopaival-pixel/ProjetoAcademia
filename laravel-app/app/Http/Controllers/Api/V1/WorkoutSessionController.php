<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TrainingPlan;
use App\Models\WorkoutSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutSessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $sessions = WorkoutSession::query()
            ->where('user_id', $user->id)
            ->orderByDesc('session_date')
            ->limit((int) min(max($request->integer('limit', 30), 1), 90))
            ->get();

        return response()->json([
            'data' => $sessions->map(fn (WorkoutSession $session) => [
                ...$this->payload($session),
            ])->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_date' => ['required', 'date'],
            'rpe_score' => ['nullable', 'integer', 'min:1', 'max:10'],
            'mood' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        $session = WorkoutSession::updateOrCreate(
            [
                'user_id' => $user->id,
                'session_date' => $validated['session_date'],
            ],
            [
                'status' => 'completed',
                'started_at' => now(),
                'ended_at' => now(),
                'completion_percent' => 100,
                'rpe_score' => $validated['rpe_score'] ?? null,
                'mood' => $validated['mood'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'data' => $this->payload($session),
        ], 201);
    }

    public function active(Request $request): JsonResponse
    {
        $session = WorkoutSession::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'paused'])
            ->latest('started_at')
            ->first();

        return response()->json([
            'data' => $session ? $this->payload($session) : null,
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $data = $request->validate([
            'training_plan_id' => ['required', 'integer', 'exists:training_plans,id'],
        ]);

        $plan = TrainingPlan::findOrFail($data['training_plan_id']);
        $this->authorize('view', $plan);

        $existing = WorkoutSession::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'paused'])
            ->first();

        if ($existing) {
            return response()->json(['data' => $this->payload($existing)]);
        }

        $session = WorkoutSession::create([
            'user_id' => $request->user()->id,
            'training_plan_id' => $plan->id,
            'session_date' => now()->toDateString(),
            'status' => 'active',
            'started_at' => now(),
            'completion_percent' => 0,
            'completed_exercise_ids' => [],
        ]);

        return response()->json(['data' => $this->payload($session)], 201);
    }

    public function updateState(Request $request, WorkoutSession $workoutSession): JsonResponse
    {
        if ((int) $workoutSession->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:active,paused,cancelled,completed'],
            'completion_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'completed_exercise_ids' => ['nullable', 'array'],
            'completed_exercise_ids.*' => ['integer'],
            'rpe_score' => ['nullable', 'integer', 'min:1', 'max:10'],
            'mood' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $workoutSession->fill([
            'status' => $data['status'],
            'completion_percent' => $data['completion_percent'] ?? $workoutSession->completion_percent,
            'completed_exercise_ids' => $data['completed_exercise_ids'] ?? $workoutSession->completed_exercise_ids,
            'rpe_score' => $data['rpe_score'] ?? $workoutSession->rpe_score,
            'mood' => $data['mood'] ?? $workoutSession->mood,
            'notes' => $data['notes'] ?? $workoutSession->notes,
        ]);

        if (in_array($data['status'], ['completed', 'cancelled'], true)) {
            $workoutSession->ended_at = now();
        }

        $workoutSession->save();

        return response()->json(['data' => $this->payload($workoutSession->fresh())]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(WorkoutSession $session): array
    {
        return [
            'id' => $session->id,
            'training_plan_id' => $session->training_plan_id,
            'session_date' => $session->session_date?->toDateString() ?? $session->session_date,
            'status' => $session->status ?? 'completed',
            'started_at' => $session->started_at?->toIso8601String(),
            'ended_at' => $session->ended_at?->toIso8601String(),
            'completion_percent' => (int) ($session->completion_percent ?? 0),
            'completed_exercise_ids' => $session->completed_exercise_ids ?? [],
            'rpe_score' => $session->rpe_score,
            'mood' => $session->mood,
            'notes' => $session->notes,
        ];
    }
}
