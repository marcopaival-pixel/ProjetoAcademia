<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsTrainingPlans;
use App\Http\Controllers\Controller;
use App\Models\TrainingPlan;
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
}
