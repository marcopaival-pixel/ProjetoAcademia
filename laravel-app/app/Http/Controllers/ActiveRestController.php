<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ActiveRestController extends Controller
{

    public function index(Request $request): View
    {
        $user = auth()->user();
        $isPremium = $user->hasPremiumAccess();
        
        // Detecção de Dia de Descanso (OFF)
        $dayOfWeek = now()->dayOfWeek; // 0 (Sun) - 6 (Sat)
        // No Laravel/Carbon, Sun=0, Mon=1...
        // No frontend costumamos usar 1-7 (Seg-Dom). Vamos normalizar.
        $dayMap = [0 => 7, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
        $todayNormalized = $dayMap[$dayOfWeek];

        $hasWorkoutToday = \App\Models\TrainingPlan::where('user_id', $user->id)
            ->where('status', 'Ativo')
            ->get()
            ->contains(function ($plan) use ($todayNormalized) {
                $days = is_array($plan->days_of_week) ? $plan->days_of_week : json_decode($plan->days_of_week, true);
                return is_array($days) && in_array($todayNormalized, $days);
            });

        $isOffDay = !$hasWorkoutToday;

        $routines = \App\Models\ActiveRestRoutine::orderBy('order')->get();

        // Sugestão Inteligente para Dia OFF
        $suggestedRoutine = null;
        if ($isOffDay) {
            $suggestedRoutine = $routines->where('category', 'Recuperação')->first() 
                ?? $routines->where('category', 'Mobilidade')->first()
                ?? $routines->first();
        }

        // Obter IDs favoritos do usuário para mostrar ícones
        $userFavorites = \App\Models\ActiveRestFavorite::where('user_id', $user->id)
            ->pluck('active_rest_routine_id')
            ->toArray();

        return view('active-rest.index', compact('routines', 'isPremium', 'userFavorites', 'isOffDay', 'suggestedRoutine'));
    }

    public function history(Request $request)
    {
        $logsQuery = ActiveRestLog::where('user_id', auth()->id());
        
        $stats = [
            'total_sessions' => $logsQuery->count(),
            'total_minutes' => floor($logsQuery->sum('duration_spent') / 60),
            'avg_score' => round($logsQuery->avg('feedback_score'), 1) ?: 0
        ];

        $logs = $logsQuery->with('routine')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('active-rest.history', compact('logs', 'stats'));
    }

    public function toggleFavorite(int $id)
    {
        $user = auth()->user();
        $favorite = \App\Models\ActiveRestFavorite::where('user_id', $user->id)
            ->where('active_rest_routine_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
        } else {
            \App\Models\ActiveRestFavorite::create([
                'user_id' => $user->id,
                'active_rest_routine_id' => $id
            ]);
            $isFavorite = true;
        }

        if (request()->ajax()) {
            return response()->json(['isFavorite' => $isFavorite]);
        }

        return back();
    }

    public function storeLog(Request $request, int $id)
    {
        $request->validate([
            'duration_spent' => 'required|integer',
            'feedback_score' => 'nullable|integer|min:1|max:5',
        ]);

        \App\Models\ActiveRestLog::create([
            'user_id' => auth()->id(),
            'active_rest_routine_id' => $id,
            'duration_spent' => $request->duration_spent,
            'feedback_score' => $request->feedback_score,
            'notes' => $request->notes
        ]);

        return response()->json(['success' => true]);
    }

    public function show(int $id): View
    {
        $routine = \App\Models\ActiveRestRoutine::findOrFail($id);

        // Gating Premium
        if ($routine->is_premium && !auth()->user()->hasPremiumAccess()) {
            return view('active-rest.premium-lock', compact('routine'));
        }

        return view('active-rest.show', compact('routine'));
    }
}
