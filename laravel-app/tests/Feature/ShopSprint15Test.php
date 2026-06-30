<?php

namespace Tests\Feature;

use App\Jobs\RefreshShopRecommendationsJob;
use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\ShopProductImage;
use App\Models\ShopRecommendation;
use App\Models\ShopVendor;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\Shop\ShopRecommendationService;
use App\Support\QueueNames;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint15Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_recommendation_service_matches_ai_tags_when_goal_types_empty(): void
    {
        $company = AcademyCompany::create(['name' => 'AI Tags', 'slug' => 'ai-tags-s15']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V AI',
            'slug' => 'v-ai-'.Str::random(4),
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat AI',
            'slug' => 'cat-ai-'.Str::random(4),
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        $tagProduct = ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Suplemento Tag IA',
            'slug' => 'tag-ia-'.Str::random(6),
            'price' => 59.90,
            'manage_stock' => true,
            'stock_quantity' => 10,
            'ai_tags' => ['emagrecimento', 'termogenico'],
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

        $this->assertTrue($products->contains(fn (ShopProduct $p) => $p->id === $tagProduct->id));

        $reco = ShopRecommendation::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('ai_tag_match', $reco->reason);
    }

    public function test_admin_can_upload_product_images_on_create(): void
    {
        Storage::fake('public');

        $company = AcademyCompany::create(['name' => 'Img Admin', 'slug' => 'img-admin-s15']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V Img',
            'slug' => 'v-img-s15',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat Img',
            'slug' => 'cat-img-s15',
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $admin = User::query()->where('is_admin', true)->firstOrFail();
        $image = UploadedFile::fake()->image('produto.jpg');

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.products.store'), [
                'name' => 'Produto com imagem',
                'vendor_id' => $vendor->id,
                'category_id' => $category->id,
                'type' => 'physical',
                'price' => 49.90,
                'status' => 'published',
                'is_active' => '1',
                'ai_tags' => 'whey, proteina',
                'product_images' => [$image],
            ])
            ->assertRedirect(route('admin.shop.products.index'));

        $product = ShopProduct::where('name', 'Produto com imagem')->firstOrFail();
        $this->assertSame(['whey', 'proteina'], $product->ai_tags);
        $this->assertSame(1, $product->images()->count());

        $stored = $product->images()->first();
        Storage::disk('public')->assertExists($stored->path);
        $this->assertTrue($stored->is_primary);
    }

    public function test_admin_can_set_primary_and_delete_product_image(): void
    {
        Storage::fake('public');

        $company = AcademyCompany::create(['name' => 'Img CRUD', 'slug' => 'img-crud-s15']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'V CRUD',
            'slug' => 'v-crud-s15',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Cat CRUD',
            'slug' => 'cat-crud-s15',
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        $product = ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Produto CRUD Img',
            'slug' => 'prod-crud-img-s15',
            'price' => 30,
            'manage_stock' => true,
            'stock_quantity' => 5,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $primary = ShopProductImage::create([
            'product_id' => $product->id,
            'path' => 'shop/products/'.$product->id.'/a.jpg',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        $secondary = ShopProductImage::create([
            'product_id' => $product->id,
            'path' => 'shop/products/'.$product->id.'/b.jpg',
            'sort_order' => 1,
            'is_primary' => false,
        ]);

        Storage::disk('public')->put($primary->path, 'a');
        Storage::disk('public')->put($secondary->path, 'b');

        User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $admin = User::query()->where('is_admin', true)->firstOrFail();

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.products.images.primary', [$product, $secondary]))
            ->assertRedirect(route('admin.shop.products.edit', $product));

        $this->assertTrue($secondary->fresh()->is_primary);
        $this->assertFalse($primary->fresh()->is_primary);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->delete(route('admin.shop.products.images.destroy', [$product, $primary]))
            ->assertRedirect(route('admin.shop.products.edit', $product));

        $this->assertDatabaseMissing('shop_product_images', ['id' => $primary->id]);
        Storage::disk('public')->assertMissing($primary->path);
        $this->assertTrue($secondary->fresh()->is_primary);
    }

    public function test_refresh_recommendations_job_uses_shop_queue(): void
    {
        Queue::fake();

        RefreshShopRecommendationsJob::dispatch(1);

        Queue::assertPushed(RefreshShopRecommendationsJob::class, function (RefreshShopRecommendationsJob $job) {
            return $job->queue === QueueNames::shop();
        });
    }

    public function test_queue_names_includes_shop(): void
    {
        $this->assertContains('shop', QueueNames::all());
        $this->assertSame('shop', QueueNames::shop());
    }
}
