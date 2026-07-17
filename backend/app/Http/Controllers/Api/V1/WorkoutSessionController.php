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
            'data' => $sessions->map(fn (WorkoutSession $session) => [
                'id' => $session->id,
                'session_date' => $session->session_date,
                'rpe_score' => $session->rpe_score,
                'mood' => $session->mood,
                'notes' => $session->notes,
            ])->values(),
        ]);
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

        return response()->json([
            'data' => [
                'id' => $session->id,
                'session_date' => $session->session_date,
                'rpe_score' => $session->rpe_score,
                'mood' => $session->mood,
                'notes' => $session->notes,
            ],
        ], 201);
    }
}
