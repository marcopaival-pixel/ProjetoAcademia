<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCategory;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ShopAdminCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ShopCategory::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.shop.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:shop_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'product_type' => 'required|in:physical,digital,service',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . rand(100, 999);
        $data['is_active'] = $request->has('is_active');

        $category = ShopCategory::create($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou a categoria do shopping: {$category->name} (#{$category->id})",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return redirect()->route('admin.shop.categories.index')->with('success', 'Categoria cadastrada com sucesso.');
    }

    public function update(Request $request, ShopCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:shop_categories,id|not_in:' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'product_type' => 'required|in:physical,digital,service',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $category->update($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Atualizou a categoria do shopping: {$category->name} (#{$category->id})",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return redirect()->route('admin.shop.categories.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(ShopCategory $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Não é possível excluir uma categoria que possui produtos cadastrados.');
        }

        $id = $category->id;
        $name = $category->name;

        $category->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Excluiu a categoria do shopping: {$name} (#{$id})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.shop.categories.index')->with('success', 'Categoria excluída com sucesso.');
    }
}
