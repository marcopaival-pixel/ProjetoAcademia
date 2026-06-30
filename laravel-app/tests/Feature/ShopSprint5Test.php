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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint5Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    private function seedShopCatalog(AcademyCompany $company): ShopProduct
    {
        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor Sprint5',
            'slug' => 'vendor-s5-'.$company->id,
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Suplementos',
            'slug' => 'sup-s5-'.$company->id,
            'product_type' => 'physical',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        return ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Whey Sprint5',
            'slug' => 'whey-'.Str::random(8),
            'price' => 100.00,
            'manage_stock' => true,
            'stock_quantity' => 20,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    private function mpSignatureHeaders(string $secret, string $dataId, string $requestId = 'req-s5-1'): array
    {
        $ts = (string) time();
        $manifest = "id:{$dataId};request-id:{$requestId};ts:{$ts}";
        $v1 = hash_hmac('sha256', $manifest, $secret);

        return [
            'x-signature' => "ts={$ts};v1={$v1}",
            'x-request-id' => $requestId,
        ];
    }

    public function test_paid_order_awards_cashback_points(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');
        SystemSetting::set('shop_points_per_real', '100');
        SystemSetting::set('shop_cashback_percent', '5');

        $company = AcademyCompany::create(['name' => 'Cashback Co', 'slug' => 'cashback-co-s5']);
        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)->post(route('shopping.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'pix',
            'shipping_method' => 'pickup',
        ])->assertRedirect();

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();
        $wallet = ShopPointsWallet::where('user_id', $user->id)->firstOrFail();

        $this->assertSame(ShopOrder::STATUS_PAID, $order->status);
        $this->assertSame(500, (int) $order->points_earned);
        $this->assertSame(500, (int) $wallet->balance_points);
    }

    public function test_points_payment_does_not_award_cashback(): void
    {
        SystemSetting::set('pagamento_ativo', 'true');
        SystemSetting::set('shop_points_per_real', '100');
        SystemSetting::set('shop_cashback_percent', '5');

        $company = AcademyCompany::create(['name' => 'No Cashback Co', 'slug' => 'no-cb-co-s5']);
        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        app(\App\Services\Shop\ShopPointsService::class)->credit($user, 20000, 'Bônus teste');

        $this->actingAs($user)->post(route('shopping.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('shopping.checkout.process'), [
            'payment_method' => 'points',
            'shipping_method' => 'pickup',
        ]);

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();

        $this->assertSame(0, (int) $order->points_earned);
        $this->assertSame(10000, (int) ShopPointsWallet::where('user_id', $user->id)->value('balance_points'));
    }

    public function test_admin_can_credit_points_to_user(): void
    {
        $company = AcademyCompany::create(['name' => 'Admin Pts Co', 'slug' => 'admin-pts-s5']);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.points.credit'), [
                'user_id' => $user->id,
                'points' => 750,
                'reason' => 'Campanha de boas-vindas',
            ])
            ->assertRedirect(route('admin.shop.points.index', ['user_id' => $user->id]));

        $wallet = ShopPointsWallet::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(750, (int) $wallet->balance_points);
    }

    public function test_mp_webhook_marks_shop_order_paid_with_valid_signature(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');
        SystemSetting::set('shop_cashback_percent', '0');

        $company = AcademyCompany::create(['name' => 'Webhook S5 Co', 'slug' => 'webhook-s5-co']);
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
        $order->update([
            'status' => ShopOrder::STATUS_PENDING,
            'paid_at' => null,
            'gateway_payment_id' => null,
            'payment_gateway' => null,
        ]);

        $secret = 'whsec-shop-s5';
        $paymentId = 'mp_shop_s5_001';
        Config::set('projeto.mp_access_token', 'test-token');
        Config::set('projeto.mp_webhook_secret', $secret);

        $this->partialMock(MercadoPagoService::class, function ($mock) use ($paymentId, $order) {
            $mock->shouldReceive('fetchPayment')
                ->once()
                ->with($paymentId)
                ->andReturn([
                    'ok' => true,
                    'payment' => [
                        'id' => $paymentId,
                        'status' => 'approved',
                        'currency_id' => 'BRL',
                        'transaction_amount' => (float) $order->total,
                        'external_reference' => 'shop:'.$order->id,
                    ],
                ]);
        });

        $payload = json_encode(['type' => 'payment', 'data' => ['id' => $paymentId]]);
        $headers = $this->mpSignatureHeaders($secret, $paymentId);

        $this->call(
            'POST',
            '/mp/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_x-signature' => $headers['x-signature'],
                'HTTP_x-request-id' => $headers['x-request-id'],
            ],
            $payload
        )->assertStatus(200)->assertSee('ok');

        $this->assertSame(ShopOrder::STATUS_PAID, $order->fresh()->status);
        $this->assertSame($paymentId, $order->fresh()->gateway_payment_id);
    }

    public function test_refund_claws_back_cashback_points(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');
        SystemSetting::set('shop_points_per_real', '100');
        SystemSetting::set('shop_cashback_percent', '5');

        $company = AcademyCompany::create(['name' => 'Clawback Co', 'slug' => 'clawback-co-s5']);
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
        $this->assertSame(500, (int) $order->points_earned);

        app(\App\Services\Shop\ShopOrderService::class)->cancel($order, 'Teste estorno cashback');

        $wallet = ShopPointsWallet::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(0, (int) $wallet->balance_points);
        $this->assertSame(ShopOrder::STATUS_REFUNDED, $order->fresh()->status);
    }
}
