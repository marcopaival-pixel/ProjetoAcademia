<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ShopVendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopAdminVendorController extends Controller
{
    public function index(): View
    {
        $vendors = ShopVendor::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('admin.shop.vendors.index', compact('vendors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedVendorData($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['approved_at'] = ($data['status'] ?? ShopVendor::STATUS_ACTIVE) === ShopVendor::STATUS_ACTIVE
            ? now()
            : null;
        $data['approved_by'] = auth()->id();

        $vendor = ShopVendor::create($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou parceiro shopping: {$vendor->name} (#{$vendor->id})",
            'ip_address' => $request->ip(),
            'payload' => ['vendor_id' => $vendor->id],
        ]);

        return redirect()->route('admin.shop.vendors.index')
            ->with('success', 'Parceiro cadastrado com sucesso.');
    }

    public function update(Request $request, ShopVendor $vendor): RedirectResponse
    {
        $data = $this->validatedVendorData($request, $vendor);

        if (($data['status'] ?? $vendor->status) === ShopVendor::STATUS_ACTIVE && $vendor->approved_at === null) {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
        }

        $vendor->update($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Atualizou parceiro shopping: {$vendor->name} (#{$vendor->id})",
            'ip_address' => $request->ip(),
            'payload' => ['vendor_id' => $vendor->id],
        ]);

        return redirect()->route('admin.shop.vendors.index')
            ->with('success', 'Parceiro atualizado com sucesso.');
    }

    public function destroy(ShopVendor $vendor): RedirectResponse
    {
        if ($vendor->products()->exists()) {
            return back()->with('error', 'Não é possível excluir um parceiro com produtos vinculados.');
        }

        $name = $vendor->name;
        $id = $vendor->id;
        $vendor->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Excluiu parceiro shopping: {$name} (#{$id})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.shop.vendors.index')
            ->with('success', 'Parceiro excluído com sucesso.');
    }

    private function validatedVendorData(Request $request, ?ShopVendor $vendor = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'document' => ['nullable', 'string', 'max:30'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:pending,active,suspended,rejected'],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'parceiro';

        do {
            $slug = $base.'-'.random_int(100, 999);
        } while (ShopVendor::where('slug', $slug)->exists());

        return $slug;
    }
}
