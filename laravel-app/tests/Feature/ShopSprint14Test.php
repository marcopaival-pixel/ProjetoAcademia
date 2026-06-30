<?php

namespace Tests\Feature;

use App\Jobs\RefreshShopRecommendationsJob;
use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\ShopRecommendation;
use App\Models\ShopSupplier;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\Shop\ShopRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint14Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_admin_can_create_product_with_supplier_and_goals(): void
    {
        $company = AcademyCompany::create(['name' => 'Product Admin', 'slug' => 'product-admin-s14']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V S14',
            'slug' => 'v-s14',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat S14',
            'slug' => 'cat-s14',
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        $supplier = ShopSupplier::create([
            'academy_company_id' => $company->id,
            'name' => 'Fornecedor S14',
            'is_active' => true,
        ]);

        User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $admin = User::query()->where('is_admin', true)->firstOrFail();

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.products.store'), [
                'name' => 'Produto com metadados',
                'vendor_id' => $vendor->id,
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'type' => 'physical',
                'price' => 99.90,
                'status' => 'published',
                'is_active' => '1',
                'goal_types' => ['hipertrofia'],
            ])
            ->assertRedirect(route('admin.shop.products.index'));

        $this->assertDatabaseHas('shop_products', [
            'name' => 'Produto com metadados',
            'supplier_id' => $supplier->id,
        ]);

        $product = ShopProduct::where('name', 'Produto com metadados')->firstOrFail();
        $this->assertSame(['hipertrofia'], $product->goal_types);
    }

    public function test_admin_can_upload_digital_product_file(): void
    {
        Storage::fake('local');

        $company = AcademyCompany::create(['name' => 'Digital Admin', 'slug' => 'digital-admin-s14']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V Digital',
            'slug' => 'v-digital-s14',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Digital Cat',
            'slug' => 'digital-cat-s14',
            'product_type' => 'digital',
            'is_active' => true,
        ]);

        User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $admin = User::query()->where('is_admin', true)->firstOrFail();
        $file = UploadedFile::fake()->create('guia-treino.pdf', 100, 'application/pdf');

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.products.store'), [
                'name' => 'Guia Digital S14',
                'vendor_id' => $vendor->id,
                'category_id' => $category->id,
                'type' => 'digital',
                'price' => 19.90,
                'status' => 'published',
                'is_active' => '1',
                'downloadable_file' => $file,
                'download_limit' => 3,
                'download_expiry_days' => 15,
            ])
            ->assertRedirect(route('admin.shop.products.index'));

        $product = ShopProduct::where('name', 'Guia Digital S14')->firstOrFail();
        $this->assertNotNull($product->downloadable_file);
        Storage::disk('local')->assertExists($product->downloadable_file);
    }

    public function test_recommendation_service_matches_user_profile_goal(): void
    {
        $company = AcademyCompany::create(['name' => 'Goal Match', 'slug' => 'goal-match-s14']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V Goal',
            'slug' => 'v-goal-'.Str::random(4),
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat Goal',
            'slug' => 'cat-goal-'.Str::random(4),
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        $goalProduct = ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Termogênico Emagrecer',
            'slug' => 'termo-'.Str::random(6),
            'price' => 79.90,
            'manage_stock' => true,
            'stock_quantity' => 10,
            'goal_types' => ['emagrecimento'],
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'goal' => 'lose',
        ]);

        $products = app(ShopRecommendationService::class)->refreshForUser($user);

        $this->assertTrue($products->contains(fn (ShopProduct $p) => $p->id === $goalProduct->id));

        $reco = ShopRecommendation::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('goal_match', $reco->reason);
    }

    public function test_paid_order_dispatches_recommendation_refresh_job(): void
    {
        Queue::fake();
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create(['name' => 'Queue Order', 'slug' => 'queue-order-s14']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V Queue',
            'slug' => 'v-queue-s14',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat Queue',
            'slug' => 'cat-queue-s14',
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        $product = ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Produto Queue',
            'slug' => 'prod-queue-s14',
            'price' => 40,
            'manage_stock' => true,
            'stock_quantity' => 5,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

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

        Queue::assertPushed(RefreshShopRecommendationsJob::class, fn ($job) => $job->userId === $user->id);
    }

    public function test_refresh_recommendations_command_sync_for_user(): void
    {
        $company = AcademyCompany::create(['name' => 'Cmd Reco', 'slug' => 'cmd-reco-s14']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V Cmd',
            'slug' => 'v-cmd-s14',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat Cmd',
            'slug' => 'cat-cmd-s14',
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Produto Cmd',
            'slug' => 'prod-cmd-s14',
            'price' => 30,
            'manage_stock' => true,
            'stock_quantity' => 5,
            'is_featured' => true,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $user = $this->userWithRole('aluno', [
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        ShopRecommendation::create([
            'user_id' => $user->id,
            'academy_company_id' => $company->id,
            'product_ids' => [999],
            'reason' => 'featured_fallback',
            'expires_at' => now()->subHour(),
        ]);

        Artisan::call('app:shop:refresh-recommendations', [
            '--user' => $user->id,
            '--sync' => true,
        ]);

        $reco = ShopRecommendation::where('user_id', $user->id)->firstOrFail();
        $this->assertTrue($reco->expires_at->isFuture());
        $this->assertNotSame([999], $reco->product_ids);
    }
}
