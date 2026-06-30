<?php

namespace Tests\Feature;

use App\Console\Commands\AuditReportCommand;
use App\Mail\ShopOrderStatusMail;
use App\Models\AcademyCompany;
use App\Models\InternalEmail;
use App\Models\ShopCategory;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Shop\ShopOrderNotificationService;
use App\Services\Shop\ShopStockAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint9Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_paid_order_sends_transactional_email(): void
    {
        Mail::fake();

        $company = AcademyCompany::create(['name' => 'Mail Co', 'slug' => 'mail-co-s9']);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::factory()->administrator()->create(['status' => 'active', 'email_verified_at' => now()]);

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

        app(ShopOrderNotificationService::class)->notifyOrderPaid($order);

        Mail::assertSent(ShopOrderStatusMail::class, function (ShopOrderStatusMail $mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->event === 'paid';
        });
    }

    public function test_stock_alert_service_lists_low_stock_products(): void
    {
        $company = AcademyCompany::create(['name' => 'Stock Co', 'slug' => 'stock-co-s9']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V S9',
            'slug' => 'v-s9',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat',
            'slug' => 'cat-s9',
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Produto Baixo',
            'slug' => 'baixo-'.Str::random(6),
            'price' => 10,
            'manage_stock' => true,
            'stock_quantity' => 2,
            'stock_alert_threshold' => 5,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $low = app(ShopStockAlertService::class)->lowStockProducts();

        $this->assertCount(1, $low);
        $this->assertSame('Produto Baixo', $low->first()->name);
    }

    public function test_stock_alert_notifies_admins(): void
    {
        $company = AcademyCompany::create(['name' => 'Notify Stock', 'slug' => 'notify-stock-s9']);

        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V Notify',
            'slug' => 'v-notify',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => ShopCategory::create([
                'academy_company_id' => $company->id,
                'name' => 'C',
                'slug' => 'c-notify',
                'product_type' => 'physical',
                'is_active' => true,
            ])->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Crítico',
            'slug' => 'crit-'.Str::random(6),
            'price' => 10,
            'manage_stock' => true,
            'stock_quantity' => 0,
            'stock_alert_threshold' => 3,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $notified = app(ShopStockAlertService::class)->notifyAdminsLowStock();

        $this->assertGreaterThanOrEqual(1, $notified);
        $this->assertTrue(
            InternalEmail::where('recipient_id', $admin->id)
                ->where('subject', 'like', '%estoque%')
                ->exists()
        );
    }

    public function test_admin_stock_page_loads(): void
    {
        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.shop.stock.index'))
            ->assertOk()
            ->assertSee('Estoque do Shopping', false);
    }

    public function test_audit_report_includes_shop_section(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'Audit Shop', 'slug' => 'audit-shop-s9']);
        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        ShopOrder::create([
            'academy_company_id' => $company->id,
            'user_id' => $user->id,
            'subtotal' => 20,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total' => 20,
            'status' => ShopOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $path = storage_path('app/reports/test-audit-s9.json');
        File::ensureDirectoryExists(dirname($path));

        Artisan::call('app:audit:report', ['--days' => 7, '--output' => $path]);

        $json = json_decode(File::get($path), true);
        File::delete($path);

        $this->assertTrue($json['shop']['available']);
        $this->assertArrayHasKey('database_orphans', $json);
        $this->assertGreaterThanOrEqual(1, $json['shop']['orders_paid_period']);
    }
}
