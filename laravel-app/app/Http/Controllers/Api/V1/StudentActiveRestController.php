<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\ActiveRestRoutine;
use App\Models\ActiveRestFavorite;
use App\Models\ActiveRestLog;
use App\Models\TrainingPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentActiveRestController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();

        if (! $isPremium) {
            return $this->success([
                'is_premium_user' => false,
                'is_off_day' => false,
                'suggested_routine_id' => null,
                'routines' => [],
            ]);
        }

        // 1. Calculate if it's an OFF day
        $dayOfWeek = now()->dayOfWeek;
        $dayMap = [0 => 7, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
        $todayNormalized = $dayMap[$dayOfWeek];

        $hasWorkoutToday = TrainingPlan::where('user_id', $user->id)
            ->where('status', 'Ativo')
            ->get()
            ->contains(function ($plan) use ($todayNormalized) {
                $days = is_array($plan->days_of_week) ? $plan->days_of_week : json_decode($plan->days_of_week, true);
                return is_array($days) && in_array($todayNormalized, $days);
            });

        $isOffDay = !$hasWorkoutToday;

        // 2. Fetch routines and user favorites
        $routines = ActiveRestRoutine::where('is_active', true)->orderBy('order')->get();
        $userFavorites = ActiveRestFavorite::where('user_id', $user->id)
            ->pluck('active_rest_routine_id')
            ->toArray();

        // 3. Smart suggestion for rest day
        $suggestedRoutine = null;
        if ($isOffDay) {
            $suggestedRoutine = $routines->where('category', 'Recuperação')->first() 
                ?? $routines->where('category', 'Mobilidade')->first()
                ?? $routines->first();
        }

        $mappedRoutines = $routines->map(fn(ActiveRestRoutine $routine) => [
            'id' => $routine->id,
            'title' => $routine->title,
            'category' => $routine->category,
            'duration' => (int) $routine->duration,
            'intensity' => $routine->intensity,
            'recommended_level' => $routine->recommended_level,
            'thumbnail' => $routine->thumbnail,
            'guide_image' => $routine->guide_image,
            'video_id' => $routine->video_id,
            'benefit' => $routine->benefit,
            'is_premium' => (bool) $routine->is_premium,
            'exercises' => $routine->exercises ?? [],
            'execution_steps' => $routine->execution_steps ?? [],
            'tips' => $routine->tips ?? [],
            'common_errors' => $routine->common_errors ?? [],
            'is_favorite' => in_array($routine->id, $userFavorites, true),
        ])->all();

        return $this->success([
            'is_premium_user' => true,
            'is_off_day' => $isOffDay,
            'suggested_routine_id' => $suggestedRoutine ? $suggestedRoutine->id : null,
            'routines' => $mappedRoutines,
        ]);
    }

    public function toggleFavorite(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (! $user->hasPremiumAccess()) {
            return $this->error('Descanso ativo é exclusivo Premium.', 403, 'premium_required');
        }
        
        $favorite = ActiveRestFavorite::where('user_id', $user->id)
            ->where('active_rest_routine_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
        } else {
            ActiveRestFavorite::create([
                'user_id' => $user->id,
                'active_rest_routine_id' => $id
            ]);
            $isFavorite = true;
        }

        return $this->success([
            'is_favorite' => $isFavorite,
        ]);
    }

    public function storeLog(Request $request, int $id): JsonResponse
    {
        if (! $request->user()->hasPremiumAccess()) {
            return $this->error('Descanso ativo é exclusivo Premium.', 403, 'premium_required');
        }

        $routine = ActiveRestRoutine::query()->findOrFail($id);
        if ($routine->is_premium && ! $request->user()->hasPremiumAccess()) {
            return $this->error('Rotina bloqueada pelo plano atual.', 403, 'premium_required');
        }

        $request->validate([
            'duration_spent' => ['required', 'integer', 'min:1'],
            'feedback_score' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $log = ActiveRestLog::create([
            'user_id' => $request->user()->id,
            'active_rest_routine_id' => $id,
            'duration_spent' => $request->input('duration_spent'),
            'feedback_score' => $request->input('feedback_score'),
        ]);

        return $this->success([
            'message' => 'Sessão registrada com sucesso.',
            'log_id' => $log->id,
        ]);
    }
}
