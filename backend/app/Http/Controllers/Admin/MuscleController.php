<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Muscle;
use App\Models\MuscleGroup;
use Illuminate\Http\Request;

class MuscleController extends Controller
{
    public function index()
    {
        $muscles = Muscle::with('group')->orderBy('name')->get();
        $groups = MuscleGroup::orderBy('name')->get();
        return view('admin.muscles.index', compact('muscles', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'group_id' => 'required|exists:muscle_groups,id',
            'type' => 'required|string|in:principal,sinergista,estabilizador',
        ]);

        Muscle::create($validated);

        return back()->with('success', 'Músculo adicionado com sucesso!');
    }

    public function update(Request $request, Muscle $muscle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'group_id' => 'required|exists:muscle_groups,id',
            'type' => 'required|string|in:principal,sinergista,estabilizador',
        ]);

        $muscle->update($validated);

        return back()->with('success', 'Músculo atualizado!');
    }

    public function destroy(Muscle $muscle)
    {
        $muscle->delete();
        return back()->with('success', 'Músculo removido!');
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:muscle_groups,name',
            'region' => 'required|string|in:superior,inferior,core,fullbody',
        ]);

        MuscleGroup::create($validated);

        return back()->with('success', 'Grupo muscular criado!');
    }
}
