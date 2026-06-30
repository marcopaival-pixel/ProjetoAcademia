<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\ShopVendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ShopSprint8Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_admin_can_create_vendor(): void
    {
        $company = AcademyCompany::create(['name' => 'Vendor Co', 'slug' => 'vendor-co-s8']);

        $admin = User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.shop.vendors.store'), [
                'name' => 'Loja Parceira S8',
                'email' => 'parceiro@s8.test',
                'commission_rate' => 12.5,
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.shop.vendors.index'));

        $this->assertDatabaseHas('shop_vendors', [
            'name' => 'Loja Parceira S8',
            'commission_rate' => 12.5,
            'status' => 'active',
        ]);
    }

    public function test_admin_cannot_delete_vendor_with_products(): void
    {
        $company = AcademyCompany::create(['name' => 'Del Vendor Co', 'slug' => 'del-vendor-s8']);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor Com Produto',
            'slug' => 'vendor-prod-s8',
            'commission_rate' => 5,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => ShopCategory::create([
                'academy_company_id' => $company->id,
                'name' => 'Cat S8',
                'slug' => 'cat-s8',
                'product_type' => 'physical',
                'is_active' => true,
            ])->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Prod S8',
            'slug' => 'prod-s8-'.Str::random(6),
            'price' => 10,
            'is_active' => true,
            'status' => ShopProduct::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->delete(route('admin.shop.vendors.destroy', $vendor))
            ->assertRedirect();

        $this->assertDatabaseHas('shop_vendors', ['id' => $vendor->id]);
    }

    public function test_admin_can_export_report_csv(): void
    {
        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.shop.reports.export', [
                'from' => now()->startOfMonth()->toDateString(),
                'to' => now()->toDateString(),
                'type' => 'summary',
            ]))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_vendors_page_loads(): void
    {
        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.shop.vendors.index'))
            ->assertOk()
            ->assertSee('Parceiros do Shopping', false);
    }
}
