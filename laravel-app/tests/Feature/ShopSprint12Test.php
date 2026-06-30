<?php

namespace Tests\Feature;

use App\Console\Commands\DatabaseTableModelGapCommand;
use App\Models\AcademyCompany;
use App\Models\Permission;
use App\Models\ShopCategory;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\ShopRecommendation;
use App\Models\ShopSupplier;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint12Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    private function createPaidOrder(AcademyCompany $company, int $userId): ShopOrder
    {
        return ShopOrder::create([
            'academy_company_id' => $company->id,
            'user_id' => $userId,
            'subtotal' => 50,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total' => 50,
            'status' => ShopOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    public function test_shop_supplier_relates_to_products(): void
    {
        $company = AcademyCompany::create(['name' => 'Supplier Co', 'slug' => 'supplier-co-s12']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor S12',
            'slug' => 'vendor-s12',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat S12',
            'slug' => 'cat-s12',
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        $supplier = ShopSupplier::create([
            'academy_company_id' => $company->id,
            'name' => 'Fornecedor Alpha',
            'is_active' => true,
        ]);

        $product = ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Produto com fornecedor',
            'slug' => 'prod-supplier-'.Str::random(6),
            'price' => 19.90,
            'manage_stock' => true,
            'stock_quantity' => 5,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->assertTrue($product->supplier->is($supplier));
        $this->assertSame(1, $supplier->products()->count());
    }

    public function test_shop_recommendation_model_persists(): void
    {
        $company = AcademyCompany::create(['name' => 'Reco Co', 'slug' => 'reco-co-s12']);
        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $reco = ShopRecommendation::create([
            'user_id' => $user->id,
            'academy_company_id' => $company->id,
            'product_ids' => [1, 2, 3],
            'reason' => 'purchase_history',
            'expires_at' => now()->addDay(),
        ]);

        $this->assertDatabaseHas('shop_recommendations', ['id' => $reco->id]);
        $this->assertFalse($reco->isExpired());
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $company = AcademyCompany::create(['name' => 'IDOR Show', 'slug' => 'idor-show-s12']);

        $owner = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $intruder = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = $this->createPaidOrder($company, $owner->id);

        $this->actingAs($intruder)
            ->get(route('shopping.orders.show', $order))
            ->assertForbidden();
    }

    public function test_user_cannot_cancel_another_users_order(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'IDOR Cancel', 'slug' => 'idor-cancel-s12']);

        $owner = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $intruder = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = $this->createPaidOrder($company, $owner->id);
        $order->update(['status' => ShopOrder::STATUS_PENDING, 'paid_at' => null]);

        $this->actingAs($intruder)
            ->post(route('shopping.orders.cancel', $order))
            ->assertForbidden();
    }

    public function test_user_cannot_access_checkout_success_of_other_order(): void
    {
        $company = AcademyCompany::create(['name' => 'IDOR Success', 'slug' => 'idor-success-s12']);

        $owner = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $intruder = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = $this->createPaidOrder($company, $owner->id);

        $this->actingAs($intruder)
            ->get(route('shopping.checkout.success', $order))
            ->assertForbidden();
    }

    public function test_tenant_admin_cannot_view_order_from_other_company(): void
    {
        $companyA = AcademyCompany::create(['name' => 'Tenant A', 'slug' => 'tenant-a-s12']);
        $companyB = AcademyCompany::create(['name' => 'Tenant B', 'slug' => 'tenant-b-s12']);

        $buyerB = $this->userWithRole('aluno', [
            'academy_company_id' => $companyB->id,
            'status' => 'active',
        ]);

        $orderB = $this->createPaidOrder($companyB, $buyerB->id);

        $tenantAdmin = $this->userWithRole('finance', [
            'is_admin' => false,
            'academy_company_id' => $companyA->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $tenantAdmin->permissions()->attach(
            Permission::query()->where('name', 'admin.access')->firstOrFail()->id
        );

        $this->actingAs($tenantAdmin)
            ->withSession(['active_role' => 'finance'])
            ->get(route('admin.shop.orders.show', $orderB))
            ->assertNotFound();
    }

    public function test_shop_table_model_gap_has_no_unmapped_shop_tables(): void
    {
        $report = DatabaseTableModelGapCommand::audit('shop_');

        $this->assertNotContains('shop_suppliers', $report['tables_without_model']);
        $this->assertNotContains('shop_recommendations', $report['tables_without_model']);
    }
}
