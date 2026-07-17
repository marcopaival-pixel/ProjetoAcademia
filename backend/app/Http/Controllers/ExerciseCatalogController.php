<?php

namespace App\Http\Controllers;

use App\Models\ExerciseEntry;
use App\Models\ExerciseCatalog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExerciseCatalogController extends Controller
{
    /**
     * Exibe o Registro de Treino (Performance HUD) para os alunos/atletas.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $date = $request->get('date', now()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);

        $entries = ExerciseEntry::where('user_id', $user->id)
            ->whereDate('entry_date', $carbonDate)
            ->get();

        $stats = [
            'total_minutes' => $entries->sum('duration_min'),
            'total_calories' => $entries->sum('calories_burned'),
            'count' => $entries->count()
        ];

        // Se quiser manter o catálogo acessível na mesma view ou em modal
        $catalog = ExerciseCatalog::where('is_active', true)
            ->orderBy('muscle_group')
            ->get();

        return view('exercises.catalog', compact('entries', 'stats', 'date', 'catalog'));
    }

    /**
     * Salva um novo registro de atividade física.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'activity_type' => 'required|string|max:120',
            'duration_min' => 'required|integer|min:1',
            'calories_burned' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'entry_date' => 'required|date',
        ]);

        $data['user_id'] = auth()->id();

        ExerciseEntry::create($data);

        return back()->with('success', 'Atividade registrada com sucesso!');
    }

    /**
     * Detalhes de um exercício específico.
     */
    public function show(ExerciseCatalog $exercise): View
    {
        if (!$exercise->is_active) {
            abort(404);
        }

        return view('exercises.show', compact('exercise'));
    }
}
