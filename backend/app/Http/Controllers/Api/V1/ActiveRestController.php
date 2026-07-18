<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActiveRestFavorite;
use App\Models\ActiveRestLog;
use App\Models\ActiveRestRoutine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActiveRestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favoriteIds = ActiveRestFavorite::where('user_id', $request->user()->id)
            ->pluck('active_rest_routine_id')
            ->all();

        $routines = ActiveRestRoutine::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => [
                'is_premium_user' => $request->user()->hasPremiumAccess(),
                'is_off_day' => false,
                'suggested_routine_id' => $routines->first()?->id,
                'routines' => $routines->map(fn (ActiveRestRoutine $routine) => [
                    'id' => $routine->id,
                    'title' => $routine->title,
                    'category' => $routine->category ?? 'Mobilidade',
                    'duration' => (int) preg_replace('/\D+/', '', (string) $routine->duration) ?: 10,
                    'intensity' => $routine->intensity,
                    'recommended_level' => $routine->recommended_level ?? null,
                    'thumbnail' => $routine->thumbnail,
                    'guide_image' => $routine->guide_image,
                    'video_id' => $routine->video_id,
                    'benefit' => $routine->benefit,
                    'is_premium' => (bool) $routine->is_premium,
                    'exercises' => $routine->exercises ?? [],
                    'execution_steps' => $routine->execution_steps ?? [],
                    'tips' => $routine->tips ?? [],
                    'common_errors' => $routine->common_errors ?? [],
                    'is_favorite' => in_array($routine->id, $favoriteIds, true),
                ])->values(),
            ],
        ]);
    }

    public function favorite(Request $request, ActiveRestRoutine $routine): JsonResponse
    {
        $favorite = ActiveRestFavorite::where('user_id', $request->user()->id)
            ->where('active_rest_routine_id', $routine->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['data' => ['is_favorite' => false]]);
        }

        ActiveRestFavorite::create([
            'user_id' => $request->user()->id,
            'active_rest_routine_id' => $routine->id,
        ]);

        return response()->json(['data' => ['is_favorite' => true]]);
    }

    public function log(Request $request, ActiveRestRoutine $routine): JsonResponse
    {
        $validated = $request->validate([
            'duration_spent' => ['required', 'integer', 'min:1'],
            'feedback_score' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $log = ActiveRestLog::create([
            'user_id' => $request->user()->id,
            'active_rest_routine_id' => $routine->id,
            'duration_spent' => $validated['duration_spent'],
            'feedback_score' => $validated['feedback_score'] ?? null,
        ]);

        return response()->json(['data' => ['id' => $log->id, 'logged' => true]], 201);
    }
}
