<?php

namespace Tests\Feature;

use App\Console\Commands\DatabaseOrphansCommand;
use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProduct;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Shop\ShopSalesReportService;
use Database\Seeders\ShopHomologSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint7Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_homolog_seeder_creates_demo_catalog(): void
    {
        $company = AcademyCompany::create(['name' => 'Homolog Co', 'slug' => 'homolog-co-s7']);

        User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->seed(ShopHomologSeeder::class);

        $this->assertDatabaseHas('shop_products', [
            'academy_company_id' => $company->id,
            'slug' => 'whey-protein-demo',
        ]);

        $this->assertDatabaseHas('shop_coupons', [
            'academy_company_id' => $company->id,
            'code' => 'DEMO10',
        ]);

        $this->assertSame('false', SystemSetting::get('pagamento_ativo'));
    }

    public function test_sales_report_calculates_revenue_and_commissions(): void
    {
        $company = AcademyCompany::create(['name' => 'Report Co', 'slug' => 'report-co-s7']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Parceiro S7',
            'slug' => 'parceiro-s7',
            'commission_rate' => 10,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = ShopOrder::create([
            'academy_company_id' => $company->id,
            'user_id' => $user->id,
            'subtotal' => 100,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total' => 100,
            'status' => ShopOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'product_id' => ShopProduct::create([
                'academy_company_id' => $company->id,
                'vendor_id' => $vendor->id,
                'category_id' => ShopCategory::create([
                    'academy_company_id' => $company->id,
                    'name' => 'Cat S7',
                    'slug' => 'cat-s7',
                    'product_type' => 'physical',
                    'is_active' => true,
                ])->id,
                'type' => ShopProduct::TYPE_PHYSICAL,
                'name' => 'Item S7',
                'slug' => 'item-s7-'.Str::random(6),
                'price' => 100,
                'is_active' => true,
                'status' => ShopProduct::STATUS_PUBLISHED,
                'published_at' => now(),
            ])->id,
            'vendor_id' => $vendor->id,
            'product_name' => 'Item S7',
            'product_type' => ShopProduct::TYPE_PHYSICAL,
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
            'commission_rate' => 10,
            'commission_amount' => 10,
            'commission_status' => 'pending',
        ]);

        $report = app(ShopSalesReportService::class)->summary(now()->startOfMonth(), now()->endOfDay());

        $this->assertSame(1, $report['order_count']);
        $this->assertSame(100.0, $report['gross_revenue']);
        $this->assertSame(10.0, $report['pending_commissions']);
        $this->assertCount(1, $report['by_vendor']);
    }

    public function test_admin_can_mark_vendor_commissions_paid(): void
    {
        $company = AcademyCompany::create(['name' => 'Pay Co', 'slug' => 'pay-co-s7']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor Pay',
            'slug' => 'vendor-pay-s7',
            'commission_rate' => 10,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = ShopOrder::create([
            'academy_company_id' => $company->id,
            'user_id' => $user->id,
            'subtotal' => 50,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total' => 50,
            'status' => ShopOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $item = ShopOrderItem::create([
            'order_id' => $order->id,
            'product_id' => ShopProduct::create([
                'academy_company_id' => $company->id,
                'vendor_id' => $vendor->id,
                'category_id' => ShopCategory::create([
                    'academy_company_id' => $company->id,
                    'name' => 'Cat Pay',
                    'slug' => 'cat-pay',
                    'product_type' => 'physical',
                    'is_active' => true,
                ])->id,
                'type' => ShopProduct::TYPE_PHYSICAL,
                'name' => 'Prod Pay',
                'slug' => 'prod-pay-'.Str::random(6),
                'price' => 50,
                'is_active' => true,
                'status' => ShopProduct::STATUS_PUBLISHED,
                'published_at' => now(),
            ])->id,
            'vendor_id' => $vendor->id,
            'product_name' => 'Prod Pay',
            'product_type' => ShopProduct::TYPE_PHYSICAL,
            'quantity' => 1,
            'unit_price' => 50,
            'total' => 50,
            'commission_rate' => 10,
            'commission_amount' => 5,
            'commission_status' => 'pending',
        ]);

        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.reports.commissions.pay'), [
                'vendor_id' => $vendor->id,
                'until' => now()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertSame('paid', $item->fresh()->commission_status);
    }

    public function test_admin_report_page_loads(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'UI Report', 'slug' => 'ui-report-s7']);

        User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->seed(ShopHomologSeeder::class);

        $admin = User::query()->where('is_admin', true)->firstOrFail();

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.shop.reports.index'))
            ->assertOk()
            ->assertSee('Relatório do Shopping', false);
    }

    public function test_orphans_command_includes_shop_checks(): void
    {
        $keys = array_column(DatabaseOrphansCommand::definedChecks(), 'key');

        $this->assertContains('shop_products_vendor', $keys);
        $this->assertContains('shop_coupon_usages_order', $keys);
        $this->assertContains('shop_points_wallets_user', $keys);
    }
}
