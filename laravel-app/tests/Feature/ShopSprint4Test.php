<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopOrder;
use App\Models\ShopPointsWallet;
use App\Models\ShopProduct;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\MercadoPagoService;
use App\Services\Shop\ShopOrderService;
use App\Services\Shop\ShopPointsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint4Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    private function seedShopCatalog(AcademyCompany $company): ShopProduct
    {
        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor Sprint4',
            'slug' => 'vendor-s4-'.$company->id,
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Suplementos',
            'slug' => 'sup-s4-'.$company->id,
            'product_type' => 'physical',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        return ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Creatina Sprint4',
            'slug' => 'creatina-'.Str::random(8),
            'price' => 50.00,
            'manage_stock' => true,
            'stock_quantity' => 20,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    private function checkoutOrder(User $user, ShopProduct $product): ShopOrder
    {
        $this->actingAs($user)->post(route('shopping.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'pix',
            'shipping_method' => 'pickup',
        ]);

        return ShopOrder::where('user_id', $user->id)->firstOrFail();
    }

    public function test_checkout_with_points_debits_wallet_and_marks_paid(): void
    {
        SystemSetting::set('pagamento_ativo', 'true');
        SystemSetting::set('shop_points_per_real', '100');

        $company = AcademyCompany::create(['name' => 'Pts Co', 'slug' => 'pts-co-s4']);
        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        app(ShopPointsService::class)->credit($user, 6000, 'Bônus teste');

        $this->actingAs($user)->post(route('shopping.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'points',
            'shipping_method' => 'pickup',
        ])->assertRedirect();

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();
        $wallet = ShopPointsWallet::where('user_id', $user->id)->firstOrFail();

        $this->assertSame(ShopOrder::STATUS_PAID, $order->status);
        $this->assertSame('points', $order->payment_method);
        $this->assertSame(1000, $wallet->balance_points);
    }

    public function test_cancel_paid_points_order_refunds_wallet(): void
    {
        SystemSetting::set('pagamento_ativo', 'true');
        SystemSetting::set('shop_points_per_real', '100');

        $company = AcademyCompany::create(['name' => 'Refund Co', 'slug' => 'refund-co-s4']);
        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        app(ShopPointsService::class)->credit($user, 6000, 'Bônus teste');

        $this->actingAs($user)->post(route('shopping.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'points',
            'shipping_method' => 'pickup',
        ]);

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)->post(route('shopping.orders.cancel', $order), [
            'reason' => 'Arrependimento',
        ])->assertRedirect();

        $order->refresh();
        $wallet = ShopPointsWallet::where('user_id', $user->id)->firstOrFail();

        $this->assertSame(ShopOrder::STATUS_REFUNDED, $order->status);
        $this->assertSame(6000, $wallet->balance_points);
    }

    public function test_cancel_pending_order_without_refund(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'Pending Co', 'slug' => 'pending-co-s4']);
        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = $this->checkoutOrder($user, $product);
        $order->update(['status' => ShopOrder::STATUS_PENDING, 'paid_at' => null]);

        app(ShopOrderService::class)->cancel($order, 'Desistência');

        $this->assertSame(ShopOrder::STATUS_CANCELLED, $order->fresh()->status);
    }

    public function test_admin_can_view_shop_orders(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'Admin Shop', 'slug' => 'admin-shop-s4']);
        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $order = $this->checkoutOrder($user, $product);

        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.shop.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number, false);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.shop.orders.show', $order))
            ->assertOk();
    }

    public function test_admin_can_refund_paid_order(): void
    {
        SystemSetting::set('pagamento_ativo', 'true');
        SystemSetting::set('shop_points_per_real', '100');

        $company = AcademyCompany::create(['name' => 'Admin Refund', 'slug' => 'admin-refund-s4']);
        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        app(ShopPointsService::class)->credit($user, 6000, 'Bônus teste');

        $this->actingAs($user)->post(route('shopping.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'points',
            'shipping_method' => 'pickup',
        ]);

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();

        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.orders.refund', $order), ['reason' => 'Admin refund'])
            ->assertRedirect();

        $this->assertSame(ShopOrder::STATUS_REFUNDED, $order->fresh()->status);
    }

    public function test_mercadopago_try_credit_premium_marks_shop_order_paid(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'Webhook Co', 'slug' => 'webhook-co-s4']);
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

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();
        $order->update(['status' => ShopOrder::STATUS_PENDING, 'paid_at' => null]);

        $result = app(MercadoPagoService::class)->tryCreditPremium([
            'id' => 'mp_shop_s4_001',
            'status' => 'approved',
            'currency_id' => 'BRL',
            'transaction_amount' => 50.00,
            'external_reference' => 'shop:'.$order->id,
        ]);

        $this->assertTrue($result['ok']);
        $this->assertSame(ShopOrder::STATUS_PAID, $order->fresh()->status);
    }
}
