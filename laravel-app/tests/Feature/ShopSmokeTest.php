<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSmokeTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    private function seedShopCatalog(AcademyCompany $company): ShopProduct
    {
        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor Teste',
            'slug' => 'vendor-teste-'.$company->id,
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Suplementos',
            'slug' => 'suplementos-'.$company->id,
            'product_type' => 'physical',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        return ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Whey Protein Teste',
            'slug' => 'whey-protein-'.Str::random(8),
            'price' => 99.90,
            'manage_stock' => true,
            'stock_quantity' => 10,
            'is_active' => true,
            'is_featured' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    public function test_shopping_index_loads_for_authenticated_user(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Academia Shop',
            'slug' => 'academia-shop-smoke',
        ]);

        $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('shopping.index'));

        $response->assertOk();
        $response->assertSee('Whey Protein Teste', false);
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Academia Cart',
            'slug' => 'academia-cart-smoke',
        ]);

        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('shopping.cart.add'), [
                'product_id' => $product->id,
                'quantity' => 2,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('shop_cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user)
            ->get(route('shopping.cart.index'))
            ->assertOk();
    }

    public function test_checkout_page_loads_with_items_in_cart(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Academia Checkout',
            'slug' => 'academia-checkout-smoke',
        ]);

        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('shopping.cart.add'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $this->actingAs($user)
            ->get(route('shopping.checkout.index'))
            ->assertOk();
    }

    public function test_checkout_process_creates_order(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create([
            'name' => 'Academia Order',
            'slug' => 'academia-order-smoke',
        ]);

        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('shopping.cart.add'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $response = $this->actingAs($user)
            ->post(route('shopping.checkout.process'), [
                'payment_method' => 'pix',
                'shipping_method' => 'pickup',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('shop_orders', [
            'user_id' => $user->id,
            'academy_company_id' => $company->id,
            'status' => ShopOrder::STATUS_PAID,
        ]);
    }

    public function test_user_can_manage_wishlist(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Academia Wishlist',
            'slug' => 'academia-wishlist-smoke',
        ]);

        $product = $this->seedShopCatalog($company);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('shopping.wishlist.toggle', $product->id))
            ->assertRedirect();

        $this->assertDatabaseHas('shop_wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)
            ->get(route('shopping.wishlist.index'))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('shopping.wishlist.toggle', $product->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('shop_wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_view_orders_after_checkout(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create([
            'name' => 'Academia Orders',
            'slug' => 'academia-orders-smoke',
        ]);

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
        ]);

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)
            ->get(route('shopping.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number, false);

        $this->actingAs($user)
            ->get(route('shopping.orders.show', $order))
            ->assertOk();
    }

    public function test_shop_payment_fulfillment_marks_order_paid(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create([
            'name' => 'Academia Pay',
            'slug' => 'academia-pay-smoke',
        ]);

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
        ]);

        $order = ShopOrder::where('user_id', $user->id)->firstOrFail();
        $order->update(['status' => ShopOrder::STATUS_PENDING, 'paid_at' => null]);

        $fulfilled = app(\App\Services\Shop\ShopPaymentFulfillmentService::class)
            ->markOrderPaidFromGateway($order->id, 'mp_shop_test_001', 'mercadopago', 99.90);

        $this->assertTrue($fulfilled);
        $this->assertSame(ShopOrder::STATUS_PAID, $order->fresh()->status);
    }

    public function test_user_cannot_add_product_from_other_tenant(): void
    {
        $companyA = AcademyCompany::create(['name' => 'Empresa A', 'slug' => 'shop-tenant-a']);
        $companyB = AcademyCompany::create(['name' => 'Empresa B', 'slug' => 'shop-tenant-b']);

        $productB = $this->seedShopCatalog($companyB);

        $userA = $this->userWithRole('aluno', [
            'academy_company_id' => $companyA->id,
            'status' => 'active',
        ]);

        $this->actingAs($userA)
            ->post(route('shopping.cart.add'), [
                'product_id' => $productB->id,
                'quantity' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('shop_cart_items', [
            'product_id' => $productB->id,
        ]);
    }
}
