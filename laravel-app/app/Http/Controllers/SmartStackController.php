<?php

namespace App\Http\Controllers;

use App\Models\SmartStack;
use App\Models\Supplement;
use App\Services\AI\OrchestratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmartStackController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    public function index()
    {
        return redirect()->route('nutrition.index', ['tab' => 'stacks']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'responsible_type' => 'required|in:ia,profissional',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $stack = SmartStack::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'status' => 'ativo',
        ]));

        return back()->with('success', 'Smart Stack criado com sucesso!');
    }

    public function update(Request $request, SmartStack $stack)
    {
        $this->authorizeOwner($stack);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:ativo,pausado,concluído',
            'goal' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $stack->update($validated);

        return back()->with('success', 'Stack atualizado!');
    }

    public function destroy(SmartStack $stack)
    {
        $this->authorizeOwner($stack);
        $stack->delete();
        return back()->with('success', 'Stack removido.');
    }

    /**
     * Adiciona suplemento ao stack.
     */
    public function addSupplement(Request $request, SmartStack $stack)
    {
        $this->authorizeOwner($stack);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'nullable|string',
            'unit' => 'nullable|string',
            'frequency' => 'nullable|string',
            'time_of_day' => 'nullable|string',
            'duration_days' => 'nullable|integer',
            'supplement_goal' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $stack->supplements()->create(array_merge($validated, [
            'user_id' => auth()->id(),
            'is_active' => true,
        ]));

        return back()->with('success', 'Suplemento adicionado ao stack!');
    }

    /**
    public function suggest(Request $request)
    {
        $user = auth()->user();
        $result = $this->orchestrator->run($user, "Sugira um Smart Stack para: " . ($request->goal ?? 'geral'), [
            'intent' => 'nutrition',
            'type' => 'supplement_suggestion',
            'clinicId' => $user->academy_company_id
        ]);

        if ($result['status'] === 'success') {
            return response()->json(['success' => true, 'suggestion' => $result['message']]);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Erro na sugestão.']);
    }

    /**
     * Adota um stack sugerido pela IA.
     */
    public function adoptSuggestion(Request $request)
    {
        $data = $request->validate([
            'suggestion' => 'required|array'
        ]);

        $suggestion = $data['suggestion'];

        DB::transaction(function() use ($suggestion) {
            $stack = SmartStack::create([
                'user_id' => auth()->id(),
                'name' => $suggestion['stack_name'],
                'goal' => $suggestion['goal'],
                'responsible_type' => 'ia',
                'status' => 'ativo',
                'notes' => 'Sugerido por NexShape AI em ' . now()->format('d/m/Y'),
            ]);

            foreach ($suggestion['supplements'] as $sup) {
                $stack->supplements()->create([
                    'user_id' => auth()->id(),
                    'name' => $sup['name'],
                    'dosage' => $sup['dosage'],
                    'unit' => $sup['unit'],
                    'frequency' => $sup['frequency'] ?? 'diário',
                    'time_of_day' => $sup['time_of_day'],
                    'supplement_goal' => $sup['goal'] ?? $suggestion['goal'],
                    'observations' => $sup['observations'],
                    'is_active' => true,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Busca suplementos no catálogo global.
     */
    public function searchCatalog(Request $request)
    {
        $query = $request->get('q');
        $supplements = \App\Models\SupplementCatalog::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($supplements);
    }

    private function authorizeOwner(SmartStack $stack)
    {
        if ($stack->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
