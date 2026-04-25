<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplementCatalog;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupplementController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupplementCatalog::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        $supplements = $query->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.supplements.index', compact('supplements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:supplements_catalog,name',
            'category' => 'nullable|string|max:255',
            'default_dosage' => 'nullable|string|max:255',
            'default_unit' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'benefits' => 'nullable|string',
            'side_effects' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $supplement = SupplementCatalog::create($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou o suplemento: {$supplement->name} (#{$supplement->id})",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return redirect()->route('admin.supplements.index')->with('success', 'Suplemento cadastrado com sucesso.');
    }

    public function update(Request $request, SupplementCatalog $supplement): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:supplements_catalog,name,' . $supplement->id,
            'category' => 'nullable|string|max:255',
            'default_dosage' => 'nullable|string|max:255',
            'default_unit' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'benefits' => 'nullable|string',
            'side_effects' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $supplement->update($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Atualizou o suplemento: {$supplement->name} (#{$supplement->id})",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return redirect()->route('admin.supplements.index')->with('success', 'Suplemento atualizado com sucesso.');
    }

    public function destroy(SupplementCatalog $supplement): RedirectResponse
    {
        $id = $supplement->id;
        $name = $supplement->name;

        $supplement->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Excluiu o suplemento: {$name} (#{$id})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.supplements.index')->with('success', 'Suplemento excluído com sucesso.');
    }

    public function toggleStatus(SupplementCatalog $supplement): RedirectResponse
    {
        $supplement->is_active = !$supplement->is_active;
        $supplement->save();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Alterou o status do suplemento #{$supplement->id} para " . ($supplement->is_active ? 'Ativo' : 'Inativo'),
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Suplemento " . ($supplement->is_active ? 'ativado' : 'desativado') . " com sucesso.");
    }
}
