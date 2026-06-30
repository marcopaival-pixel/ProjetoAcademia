<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\ShopProductImage;
use App\Models\ShopSupplier;
use App\Models\ShopVendor;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ShopAdminProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = ShopProduct::query()->with(['category', 'vendor', 'supplier', 'images']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $products = $query->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = ShopCategory::active()->orderBy('name')->get();
        $vendors = ShopVendor::where('status', 'active')->orderBy('name')->get();
        $suppliers = ShopSupplier::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.shop.products.index', compact('products', 'categories', 'vendors', 'suppliers'));
    }

    public function create(): View
    {
        $categories = ShopCategory::active()->orderBy('name')->get();
        $vendors = ShopVendor::where('status', 'active')->orderBy('name')->get();
        $suppliers = ShopSupplier::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.shop.products.create', compact('categories', 'vendors', 'suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);
        $data = $this->applyProductExtras($request, $data);

        $data['slug'] = Str::slug($data['name']) . '-' . rand(1000, 9999);

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $product = ShopProduct::create($data);

        $this->storeUploadedImages($request, $product);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou o produto no shopping: {$product->name} (#{$product->id})",
            'ip_address' => $request->ip(),
            'payload' => $this->auditPayload($data),
        ]);

        return redirect()->route('admin.shop.products.index')->with('success', 'Produto cadastrado com sucesso.');
    }

    public function edit(ShopProduct $product): View
    {
        $product->load('images', 'supplier');

        $categories = ShopCategory::active()->orderBy('name')->get();
        $vendors = ShopVendor::where('status', 'active')->orderBy('name')->get();
        $suppliers = ShopSupplier::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.shop.products.edit', compact('product', 'categories', 'vendors', 'suppliers'));
    }

    public function update(Request $request, ShopProduct $product): RedirectResponse
    {
        $data = $this->validateProduct($request);
        $data = $this->applyProductExtras($request, $data, $product);

        if ($data['status'] === 'published' && !$product->published_at) {
            $data['published_at'] = now();
        }

        $product->update($data);

        $this->storeUploadedImages($request, $product);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Atualizou o produto do shopping: {$product->name} (#{$product->id})",
            'ip_address' => $request->ip(),
            'payload' => $this->auditPayload($data),
        ]);

        return redirect()->route('admin.shop.products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(ShopProduct $product): RedirectResponse
    {
        $id = $product->id;
        $name = $product->name;

        $product->load('images');

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Excluiu o produto do shopping: {$name} (#{$id})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.shop.products.index')->with('success', 'Produto excluído com sucesso.');
    }

    public function storeImages(Request $request, ShopProduct $product): RedirectResponse
    {
        $request->validate([
            'product_images' => 'required|array|min:1',
            'product_images.*' => 'image|max:5120',
        ]);

        $this->storeUploadedImages($request, $product);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Adicionou imagens ao produto shopping: {$product->name} (#{$product->id})",
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('admin.shop.products.edit', $product)
            ->with('success', 'Imagens adicionadas com sucesso.');
    }

    public function destroyImage(Request $request, ShopProduct $product, ShopProductImage $image): RedirectResponse
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->path);

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $next = $product->images()->orderBy('sort_order')->first();
            if ($next !== null) {
                $next->update(['is_primary' => true]);
            }
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Removeu imagem do produto shopping: {$product->name} (#{$product->id})",
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('admin.shop.products.edit', $product)
            ->with('success', 'Imagem removida com sucesso.');
    }

    public function setPrimaryImage(Request $request, ShopProduct $product, ShopProductImage $image): RedirectResponse
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return redirect()
            ->route('admin.shop.products.edit', $product)
            ->with('success', 'Imagem principal atualizada.');
    }

    private function validateProduct(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'vendor_id' => 'required|exists:shop_vendors,id',
            'category_id' => 'required|exists:shop_categories,id',
            'supplier_id' => 'nullable|exists:shop_suppliers,id',
            'type' => 'required|in:physical,digital,service',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost_price' => 'nullable|numeric|min:0',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'required_if:manage_stock,1|nullable|integer|min:0',
            'stock_alert_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'requires_scheduling' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'status' => 'required|in:draft,pending_review,published,archived',
            'goal_types' => 'nullable|array',
            'goal_types.*' => 'in:emagrecimento,hipertrofia,performance,saude',
            'ai_tags' => 'nullable|string|max:500',
            'product_images' => 'nullable|array',
            'product_images.*' => 'image|max:5120',
            'downloadable_file' => 'nullable|file|max:51200',
            'download_limit' => 'nullable|integer|min:1|max:100',
            'download_expiry_days' => 'nullable|integer|min:1|max:3650',
        ]);

        $data['manage_stock'] = $request->has('manage_stock');
        $data['requires_scheduling'] = $request->has('requires_scheduling');
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        return $data;
    }

    private function applyProductExtras(Request $request, array $data, ?ShopProduct $product = null): array
    {
        $goalTypes = $request->input('goal_types', []);
        $data['goal_types'] = is_array($goalTypes) && $goalTypes !== []
            ? array_values(array_unique($goalTypes))
            : null;

        $data['supplier_id'] = $request->filled('supplier_id')
            ? (int) $request->input('supplier_id')
            : null;

        $data['ai_tags'] = $this->parseAiTags($request->input('ai_tags'));

        if (($data['type'] ?? $product?->type) === ShopProduct::TYPE_DIGITAL) {
            $data['download_limit'] = $request->filled('download_limit')
                ? (int) $request->input('download_limit')
                : null;
            $data['download_expiry_days'] = $request->filled('download_expiry_days')
                ? (int) $request->input('download_expiry_days')
                : null;

            if ($request->hasFile('downloadable_file')) {
                if ($product?->downloadable_file) {
                    Storage::disk('local')->delete($product->downloadable_file);
                }

                $data['downloadable_file'] = $request->file('downloadable_file')
                    ->store('shop/downloads', 'local');
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function auditPayload(array $data): array
    {
        unset($data['downloadable_file'], $data['product_images']);

        return $data;
    }

    private function parseAiTags(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $tags = array_values(array_unique(array_filter(array_map(
            static fn (string $tag) => strtolower(trim($tag)),
            preg_split('/[,;]+/', $raw) ?: []
        ))));

        return $tags !== [] ? $tags : null;
    }

    private function storeUploadedImages(Request $request, ShopProduct $product, string $field = 'product_images'): void
    {
        if (! $request->hasFile($field)) {
            return;
        }

        $files = $request->file($field);
        if (! is_array($files)) {
            $files = [$files];
        }

        $existingCount = $product->images()->count();
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($files as $index => $file) {
            if ($file === null) {
                continue;
            }

            $path = $file->store("shop/products/{$product->id}", 'public');
            $isPrimary = ! $hasPrimary && $index === 0;

            ShopProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'alt' => $product->name,
                'sort_order' => $existingCount + $index,
                'is_primary' => $isPrimary,
            ]);

            if ($isPrimary) {
                $hasPrimary = true;
            }
        }
    }
}
