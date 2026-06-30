<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProduct;
use App\Models\ShopRecommendation;
use App\Models\ShopSupplier;
use App\Models\ShopVendor;
use App\Models\User;
use App\Services\Shop\ShopRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint13Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    private function seedCatalog(AcademyCompany $company, array $productOverrides = []): ShopProduct
    {
        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor S13',
            'slug' => 'vendor-s13-'.$company->id.'-'.Str::random(4),
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat S13',
            'slug' => 'cat-s13-'.$company->id.'-'.Str::random(4),
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        return ShopProduct::create(array_merge([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Produto S13',
            'slug' => 'prod-s13-'.Str::random(6),
            'price' => 49.90,
            'manage_stock' => true,
            'stock_quantity' => 10,
            'is_active' => true,
            'is_featured' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ], $productOverrides));
    }

    public function test_admin_can_create_supplier(): void
    {
        $company = AcademyCompany::create(['name' => 'Supplier Admin', 'slug' => 'supplier-admin-s13']);

        User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $admin = User::query()->where('is_admin', true)->firstOrFail();

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.suppliers.store'), [
                'name' => 'Distribuidora Fit',
                'contact_name' => 'João',
                'email' => 'joao@fit.example',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.shop.suppliers.index'));

        $this->assertDatabaseHas('shop_suppliers', [
            'name' => 'Distribuidora Fit',
            'contact_name' => 'João',
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_delete_supplier_with_linked_products(): void
    {
        $company = AcademyCompany::create(['name' => 'Supplier Del', 'slug' => 'supplier-del-s13']);

        $supplier = ShopSupplier::create([
            'academy_company_id' => $company->id,
            'name' => 'Fornecedor Vinculado',
            'is_active' => true,
        ]);

        $this->seedCatalog($company, ['supplier_id' => $supplier->id, 'name' => 'Com fornecedor']);

        User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $admin = User::query()->where('is_admin', true)->firstOrFail();

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->delete(route('admin.shop.suppliers.destroy', $supplier))
            ->assertRedirect();

        $this->assertDatabaseHas('shop_suppliers', ['id' => $supplier->id]);
    }

    public function test_recommendation_service_uses_featured_fallback_without_purchase_history(): void
    {
        $company = AcademyCompany::create(['name' => 'Reco Fallback', 'slug' => 'reco-fallback-s13']);
        $featured = $this->seedCatalog($company, [
            'name' => 'Destaque Recomendado',
            'is_featured' => true,
        ]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $products = app(ShopRecommendationService::class)->recommendedProductsFor($user, 4);

        $this->assertTrue($products->contains(fn (ShopProduct $p) => $p->id === $featured->id));

        $reco = ShopRecommendation::where('user_id', $user->id)->first();
        $this->assertNotNull($reco);
        $this->assertSame('featured_fallback', $reco->reason);
        $this->assertTrue($reco->expires_at->isFuture());
    }

    public function test_recommendation_service_uses_purchase_history(): void
    {
        $company = AcademyCompany::create(['name' => 'Reco History', 'slug' => 'reco-history-s13']);

        $purchased = $this->seedCatalog($company, ['name' => 'Já comprado', 'slug' => 'comprado-s13-'.Str::random(4)]);
        $related = $this->seedCatalog($company, [
            'name' => 'Sugestão mesma categoria',
            'slug' => 'sugestao-s13-'.Str::random(4),
            'category_id' => $purchased->category_id,
        ]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = ShopOrder::create([
            'academy_company_id' => $company->id,
            'user_id' => $user->id,
            'subtotal' => 49.90,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total' => 49.90,
            'status' => ShopOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'product_id' => $purchased->id,
            'vendor_id' => $purchased->vendor_id,
            'product_name' => $purchased->name,
            'product_type' => ShopProduct::TYPE_PHYSICAL,
            'quantity' => 1,
            'unit_price' => 49.90,
            'discount_amount' => 0,
            'total' => 49.90,
        ]);

        $products = app(ShopRecommendationService::class)->recommendedProductsFor($user, 6);

        $this->assertTrue($products->contains(fn (ShopProduct $p) => $p->id === $related->id));
        $this->assertFalse($products->contains(fn (ShopProduct $p) => $p->id === $purchased->id));

        $this->assertDatabaseHas('shop_recommendations', [
            'user_id' => $user->id,
            'reason' => 'purchase_history',
        ]);
    }

    public function test_recommendation_service_reuses_valid_cache(): void
    {
        $company = AcademyCompany::create(['name' => 'Reco Cache', 'slug' => 'reco-cache-s13']);
        $this->seedCatalog($company, ['name' => 'Cache Prod']);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $service = app(ShopRecommendationService::class);
        $first = $service->recommendedProductsFor($user);
        $second = $service->recommendedProductsFor($user);

        $this->assertSame($first->pluck('id')->all(), $second->pluck('id')->all());
        $this->assertSame(1, ShopRecommendation::count());
    }

    public function test_shopping_index_shows_recommendations_section(): void
    {
        $company = AcademyCompany::create(['name' => 'Index Reco', 'slug' => 'index-reco-s13']);
        $this->seedCatalog($company, ['name' => 'Produto Vitrine Reco', 'is_featured' => true]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('shopping.index'))
            ->assertOk()
            ->assertSee('Recomendados para você', false)
            ->assertSee('Produto Vitrine Reco', false);
    }
}
