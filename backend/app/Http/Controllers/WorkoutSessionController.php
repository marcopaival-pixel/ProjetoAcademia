<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkoutSession;

class WorkoutSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'session_date' => 'required|date',
            'rpe_score' => 'required|integer|min:1|max:10',
            'mood' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        WorkoutSession::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'session_date' => $request->session_date
            ],
            [
                'rpe_score' => $request->rpe_score,
                'mood' => $request->mood,
                'notes' => $request->notes
            ]
        );

        return back()->with('success', 'Percepção de esforço registrada com sucesso! Excelente treino.');
    }
}
