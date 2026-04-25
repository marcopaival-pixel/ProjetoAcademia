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
        
        $query = \App\Models\ActiveRestRoutine::where('is_active', true);

        // Filtros
        if ($request->has('category') && $request->category != 'Todos') {
            $query->where('category', $request->category);
        }

        if ($request->has('level') && $request->level != 'Todos') {
            $query->where('recommended_level', $request->level);
        }

        if ($request->has('duration')) {
            if ($request->duration == '5') {
                $query->where('duration', 'like', '5%');
            } elseif ($request->duration == '10') {
                $query->where('duration', 'like', '10%');
            } elseif ($request->duration == '15') {
                $query->where('duration', 'like', '15%');
            }
        }

        if ($request->has('favorites') && $request->favorites == '1') {
            $favoriteIds = \App\Models\ActiveRestFavorite::where('user_id', $user->id)
                ->pluck('active_rest_routine_id');
            $query->whereIn('id', $favoriteIds);
        }

        $routines = $query->orderBy('order')->get();

        // Obter IDs favoritos do usuário para mostrar ícones
        $userFavorites = \App\Models\ActiveRestFavorite::where('user_id', $user->id)
            ->pluck('active_rest_routine_id')
            ->toArray();

        return view('active-rest.index', compact('routines', 'isPremium', 'userFavorites'));
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
