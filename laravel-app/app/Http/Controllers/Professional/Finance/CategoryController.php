<?php

namespace App\Http\Controllers\Professional\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', \App\Models\ProfessionalFinanceCategory::class);

        $categories = \App\Models\ProfessionalFinanceCategory::where('professional_id', auth()->id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('professional.finance.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:revenue,expense',
        ]);

        \App\Models\ProfessionalFinanceCategory::create([
            'professional_id' => auth()->id(),
            'name' => $request->name,
            'type' => $request->type,
            'is_default' => false,
        ]);

        return back()->with('success', 'Categoria adicionada com sucesso.');
    }

    public function update(Request $request, \App\Models\ProfessionalFinanceCategory $category)
    {
        $this->authorize('update', $category);

        if ($category->is_default) {
            return back()->with('error', 'Não é possível editar uma categoria padrão.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:revenue,expense',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return back()->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(\App\Models\ProfessionalFinanceCategory $category)
    {
        $this->authorize('delete', $category);

        if ($category->is_default) {
            return back()->with('error', 'Não é possível excluir uma categoria padrão.');
        }

        if ($category->entries()->exists()) {
            return back()->with('error', 'Não é possível excluir esta categoria pois ela possui lançamentos vinculados.');
        }

        $category->delete();

        return back()->with('success', 'Categoria excluída com sucesso.');
    }
}
