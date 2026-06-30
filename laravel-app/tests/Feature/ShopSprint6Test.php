<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\InternalEmail;
use App\Models\ShopCategory;
use App\Models\ShopCoupon;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AgendaNotificationService;
use App\Services\Shop\ShopOrderNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint6Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    private function seedShopCatalog(AcademyCompany $company, float $price = 100.00): ShopProduct
    {
        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor Sprint6',
            'slug' => 'vendor-s6-'.$company->id,
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Suplementos',
            'slug' => 'sup-s6-'.$company->id,
            'product_type' => 'physical',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        return ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Produto Sprint6',
            'slug' => 'prod-s6-'.Str::random(8),
            'price' => $price,
            'manage_stock' => true,
            'stock_quantity' => 20,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    public function test_admin_can_create_coupon(): void
    {
        $company = AcademyCompany::create(['name' => 'Coupon Co', 'slug' => 'coupon-co-s6']);

        $admin = User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.coupons.store'), [
                'code' => 'VERAO10',
                'description' => 'Desconto de verão',
                'type' => 'percentage',
                'discount_value' => 10,
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.shop.coupons.index'));

        $this->assertDatabaseHas('shop_coupons', [
            'code' => 'VERAO10',
            'academy_company_id' => $company->id,
            'type' => 'percentage',
        ]);
    }

    public function test_checkout_applies_coupon_discount(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'Discount Co', 'slug' => 'disc-co-s6']);
        $product = $this->seedShopCatalog($company, 100.00);

        $admin = User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        ShopCoupon::create([
            'academy_company_id' => $company->id,
            'created_by' => $admin->id,
            'code' => 'DESC10',
            'type' => ShopCoupon::TYPE_PERCENTAGE,
            'discount_value' => 10,
            'status' => 'active',
        ]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)->post(route('shopping.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('shopping.cart.coupon.apply'), [
            'code' => 'DESC10',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'pix',
            'shipping_method' => 'pickup',
        ])->assertRedirect();

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();

        $this->assertSame(10.0, (float) $order->discount_amount);
        $this->assertSame(90.0, (float) $order->total);
    }

    public function test_paid_order_sends_internal_notification(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');
        SystemSetting::set('shop_cashback_percent', '0');

        $company = AcademyCompany::create(['name' => 'Notify Co', 'slug' => 'notify-co-s6']);
        $product = $this->seedShopCatalog($company);

        User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)->post(route('shopping.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'pix',
            'shipping_method' => 'pickup',
        ]);

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();

        $this->assertTrue(
            InternalEmail::where('recipient_id', $user->id)
                ->where('subject', 'like', '%'.$order->order_number.'%')
                ->exists()
        );
    }

    public function test_user_can_view_points_wallet_page(): void
    {
        $company = AcademyCompany::create(['name' => 'Wallet Co', 'slug' => 'wallet-co-s6']);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        app(\App\Services\Shop\ShopPointsService::class)->credit($user, 500, 'Bônus teste');

        $this->actingAs($user)
            ->get(route('shopping.points.index'))
            ->assertOk()
            ->assertSee('500', false);
    }

    public function test_admin_orders_index_shows_stats(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'Stats Co', 'slug' => 'stats-co-s6']);
        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)->post(route('shopping.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'pix',
            'shipping_method' => 'pickup',
        ]);

        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.shop.orders.index'))
            ->assertOk()
            ->assertSee('Receita hoje', false);
    }

    public function test_shipped_notification_service_sends_message(): void
    {
        User::factory()->administrator()->create(['status' => 'active', 'email_verified_at' => now()]);

        $company = AcademyCompany::create(['name' => 'Ship Co', 'slug' => 'ship-co-s6']);
        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = ShopOrder::create([
            'academy_company_id' => $company->id,
            'user_id' => $user->id,
            'order_number' => 'NS-TEST-S6',
            'subtotal' => 50,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total' => 50,
            'status' => ShopOrder::STATUS_PAID,
            'paid_at' => now(),
            'tracking_code' => 'BR123',
        ]);

        app(ShopOrderNotificationService::class)->notifyOrderShipped($order);

        $this->assertTrue(
            InternalEmail::where('recipient_id', $user->id)
                ->where('subject', 'like', '%enviado%')
                ->exists()
        );
    }

    public function test_agenda_notification_notifies_waitlist_on_cancel(): void
    {
        User::factory()->administrator()->create(['status' => 'active', 'email_verified_at' => now()]);

        $company = AcademyCompany::create(['name' => 'Agenda Co', 'slug' => 'agenda-co-s6']);

        $professional = $this->userWithRole('professional', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $patient = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $waitlistPatient = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email' => 'waitlist-s6@example.com',
        ]);

        $appointmentAt = now()->addDays(3)->setTime(10, 0);

        $appointment = \App\Models\ProfessionalAppointment::create([
            'professional_id' => $professional->id,
            'patient_id' => $patient->id,
            'appointment_at' => $appointmentAt,
            'service_type' => 'Consulta',
            'status' => \App\Models\ProfessionalAppointment::STATUS_SCHEDULED,
        ]);

        \App\Models\AppointmentWaitlist::create([
            'patient_id' => $waitlistPatient->id,
            'professional_id' => $professional->id,
            'requested_date' => $appointmentAt,
            'status' => 'waiting',
        ]);

        app(AgendaNotificationService::class)->notifyWaitlistOnCancellation($appointment);

        $this->assertTrue(
            InternalEmail::where('recipient_id', $waitlistPatient->id)
                ->where('subject', 'like', '%Vaga disponível%')
                ->exists()
        );
    }
}
