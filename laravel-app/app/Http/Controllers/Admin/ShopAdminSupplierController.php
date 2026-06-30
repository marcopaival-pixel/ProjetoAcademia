<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ShopSupplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopAdminSupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = ShopSupplier::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('admin.shop.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedSupplierData($request);
        $supplier = ShopSupplier::create($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou fornecedor shopping: {$supplier->name} (#{$supplier->id})",
            'ip_address' => $request->ip(),
            'payload' => ['supplier_id' => $supplier->id],
        ]);

        return redirect()->route('admin.shop.suppliers.index')
            ->with('success', 'Fornecedor cadastrado com sucesso.');
    }

    public function update(Request $request, ShopSupplier $supplier): RedirectResponse
    {
        $data = $this->validatedSupplierData($request);
        $supplier->update($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Atualizou fornecedor shopping: {$supplier->name} (#{$supplier->id})",
            'ip_address' => $request->ip(),
            'payload' => ['supplier_id' => $supplier->id],
        ]);

        return redirect()->route('admin.shop.suppliers.index')
            ->with('success', 'Fornecedor atualizado com sucesso.');
    }

    public function destroy(ShopSupplier $supplier): RedirectResponse
    {
        if ($supplier->products()->exists()) {
            return back()->with('error', 'Não é possível excluir um fornecedor com produtos vinculados.');
        }

        $name = $supplier->name;
        $id = $supplier->id;
        $supplier->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Excluiu fornecedor shopping: {$name} (#{$id})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.shop.suppliers.index')
            ->with('success', 'Fornecedor excluído com sucesso.');
    }

    private function validatedSupplierData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:30'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
