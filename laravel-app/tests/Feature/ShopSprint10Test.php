<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\Permission;
use App\Models\ShopCategory;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProduct;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use App\Services\MenuAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint10Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    private function seedDigitalProduct(AcademyCompany $company, string $storagePath): ShopProduct
    {
        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor S10',
            'slug' => 'vendor-s10-'.$company->id,
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Digitais',
            'slug' => 'digitais-s10-'.$company->id,
            'product_type' => 'digital',
            'is_active' => true,
        ]);

        return ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_DIGITAL,
            'name' => 'E-book Treino',
            'slug' => 'ebook-'.Str::random(8),
            'price' => 29.90,
            'manage_stock' => false,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
            'downloadable_file' => $storagePath,
        ]);
    }

    public function test_owner_can_download_digital_product_file(): void
    {
        Storage::fake('local');
        $storagePath = 'shop/downloads/ebook-test.pdf';
        Storage::disk('local')->put($storagePath, 'conteudo digital de teste');

        $company = AcademyCompany::create(['name' => 'Download Co', 'slug' => 'download-co-s10']);
        $product = $this->seedDigitalProduct($company, $storagePath);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $token = (string) Str::uuid();

        $order = ShopOrder::create([
            'academy_company_id' => $company->id,
            'user_id' => $user->id,
            'subtotal' => 29.90,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total' => 29.90,
            'status' => ShopOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'vendor_id' => $product->vendor_id,
            'product_name' => $product->name,
            'product_type' => ShopProduct::TYPE_DIGITAL,
            'quantity' => 1,
            'unit_price' => 29.90,
            'discount_amount' => 0,
            'total' => 29.90,
            'download_token' => $token,
            'download_count' => 0,
        ]);

        $response = $this->actingAs($user)
            ->get(route('shopping.orders.download', $token));

        $response->assertOk();
        $this->assertSame(1, ShopOrderItem::where('download_token', $token)->value('download_count'));
    }

    public function test_other_user_cannot_download_digital_product(): void
    {
        Storage::fake('local');
        $storagePath = 'shop/downloads/ebook-forbidden.pdf';
        Storage::disk('local')->put($storagePath, 'conteudo protegido');

        $company = AcademyCompany::create(['name' => 'Forbidden Co', 'slug' => 'forbidden-co-s10']);
        $product = $this->seedDigitalProduct($company, $storagePath);

        $owner = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $intruder = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $token = (string) Str::uuid();

        $order = ShopOrder::create([
            'academy_company_id' => $company->id,
            'user_id' => $owner->id,
            'subtotal' => 29.90,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total' => 29.90,
            'status' => ShopOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'vendor_id' => $product->vendor_id,
            'product_name' => $product->name,
            'product_type' => ShopProduct::TYPE_DIGITAL,
            'quantity' => 1,
            'unit_price' => 29.90,
            'discount_amount' => 0,
            'total' => 29.90,
            'download_token' => $token,
        ]);

        $this->actingAs($intruder)
            ->get(route('shopping.orders.download', $token))
            ->assertForbidden();
    }

    public function test_tenant_admin_with_panel_access_can_open_shop_products(): void
    {
        $company = AcademyCompany::create(['name' => 'Admin Shop Co', 'slug' => 'admin-shop-s10']);

        $tenantAdmin = $this->userWithRole('finance', [
            'is_admin' => false,
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $tenantAdmin->permissions()->attach(
            Permission::query()->where('name', 'admin.access')->firstOrFail()->id
        );

        $this->actingAs($tenantAdmin)
            ->withSession(['active_role' => 'finance'])
            ->get(route('admin.shop.products.index'))
            ->assertOk();
    }

    public function test_menu_access_service_allows_admin_shop_for_tenant_admin(): void
    {
        $company = AcademyCompany::create(['name' => 'Menu Shop Co', 'slug' => 'menu-shop-s10']);

        $tenantAdmin = $this->userWithRole('finance', [
            'is_admin' => false,
            'academy_company_id' => $company->id,
        ]);

        $tenantAdmin->permissions()->attach(
            Permission::query()->where('name', 'admin.access')->firstOrFail()->id
        );

        $allowed = app(MenuAccessService::class)->canAccessRoute(
            $tenantAdmin,
            'admin.shop.orders.index',
            request()
        );

        $this->assertTrue($allowed);
    }

    public function test_smoke_command_includes_shopping_routes(): void
    {
        $this->seedRbac();

        $exitCode = Artisan::call('app:smoke:test', ['--target' => 'homologacao']);
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('shopping.index', $output);
        $this->assertStringContainsString('admin.shop.products.index', $output);
    }
}
