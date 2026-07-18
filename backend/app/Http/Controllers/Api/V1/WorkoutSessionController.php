<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
            'data' => $sessions->map(fn (WorkoutSession $session) => $this->payload($session))->values(),
        ]);
    }

    public function active(Request $request): JsonResponse
    {
        $session = WorkoutSession::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('session_date', today())
            ->latest('id')
            ->first();

        return response()->json(['data' => $session ? $this->payload($session) : null]);
    }

    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'training_plan_id' => ['required', 'integer'],
        ]);

        $session = WorkoutSession::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'session_date' => today()->toDateString(),
            ],
            [
                'rpe_score' => null,
                'mood' => null,
                'notes' => null,
            ]
        );

        return response()->json(['data' => $this->payload($session)], 201);
    }

    public function update(Request $request, WorkoutSession $session): JsonResponse
    {
        abort_unless((int) $session->user_id === (int) $request->user()->id, 403);

        $validated = $request->validate([
            'rpe_score' => ['nullable', 'integer', 'min:1', 'max:10'],
            'mood' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $session->update($validated);

        return response()->json(['data' => $this->payload($session->refresh())]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_date' => ['required', 'date'],
            'rpe_score' => ['required', 'integer', 'min:1', 'max:10'],
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
                'rpe_score' => $validated['rpe_score'],
                'mood' => $validated['mood'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json(['data' => $this->payload($session)], 201);
    }

    private function payload(WorkoutSession $session): array
    {
        return [
            'id' => $session->id,
            'training_plan_id' => null,
            'session_date' => $session->session_date,
            'status' => 'active',
            'started_at' => optional($session->created_at)->toISOString(),
            'ended_at' => null,
            'completion_percent' => 0,
            'completed_exercise_ids' => [],
            'rpe_score' => $session->rpe_score ? (int) $session->rpe_score : null,
            'mood' => $session->mood,
            'notes' => $session->notes,
        ];
    }
}
