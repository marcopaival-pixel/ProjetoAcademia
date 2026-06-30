<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\AcademyCompany;
use App\Models\Clinic;
use App\Models\Plan;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\ShopCategory;
use App\Models\ShopVendor;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

/**
 * Smoke de release — fluxos críticos para homologação e go-live.
 *
 * @group release
 */
class ReleaseSmokeTest extends TestCase
{
    use RefreshDatabase, SeedsRbacForTests;

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_login_page_is_public(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_authenticated_aluno_reaches_patient_reports_index(): void
    {
        $user = $this->userWithRole('aluno');

        $this->actingAs($user)
            ->get(route('patient.reports.index'))
            ->assertOk();
    }

    public function test_representative_dashboard_requires_representative_role(): void
    {
        $aluno = $this->userWithRole('aluno');
        $representative = $this->userWithRole('representative');

        $this->actingAs($aluno)
            ->get(route('representative.dashboard'))
            ->assertForbidden();

        $this->actingAs($representative)
            ->get(route('representative.dashboard'))
            ->assertOk();
    }

    public function test_professional_finance_dashboard_requires_professional(): void
    {
        $professional = $this->userWithRole('professional');

        $this->actingAs($professional)
            ->followingRedirects()
            ->get(route('professional.finance.dashboard'))
            ->assertOk();
    }

    public function test_free_checkout_smoke_activates_subscription(): void
    {
        AdminSetting::set('pagamento_ativo', 'false');

        $plan = Plan::create([
            'name' => 'Smoke Plan',
            'type' => 'student',
            'price' => 0,
        ]);
        $plan->forceFill(['is_active' => true])->save();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'registration_approval_status' => 'approved',
        ]);

        $this->actingAs($user)
            ->postJson(route('checkout.process'), ['plan_id' => $plan->id])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_two_clinics_remain_isolated_for_training_plans(): void
    {
        $clinicA = Clinic::create(['name' => 'Smoke A', 'slug' => 'smoke-a', 'status' => 'active']);
        $clinicB = Clinic::create(['name' => 'Smoke B', 'slug' => 'smoke-b', 'status' => 'active']);

        $userA = User::factory()->create(['clinic_id' => $clinicA->id]);
        $userB = User::factory()->create(['clinic_id' => $clinicB->id]);

        $planB = \App\Models\TrainingPlan::create([
            'user_id' => $userB->id,
            'clinic_id' => $clinicB->id,
            'name' => 'Plano isolado B',
            'status' => 'active',
        ]);

        $this->actingAs($userA);
        $this->assertFalse(\App\Models\TrainingPlan::where('id', $planB->id)->exists());
    }

    public function test_shopping_checkout_smoke_creates_paid_order(): void
    {
        SystemSetting::set('pagamento_ativo', 'false');

        $company = AcademyCompany::create([
            'name' => 'Release Shop',
            'slug' => 'release-shop-smoke',
        ]);

        $vendor = ShopVendor::create([
            'academy_company_id' => $company->id,
            'name' => 'Vendor Release',
            'slug' => 'vendor-release',
            'commission_rate' => 0,
            'status' => ShopVendor::STATUS_ACTIVE,
            'approved_at' => now(),
        ]);

        $category = ShopCategory::create([
            'academy_company_id' => $company->id,
            'name' => 'Release Cat',
            'slug' => 'release-cat',
            'product_type' => 'physical',
            'is_active' => true,
        ]);

        $product = ShopProduct::create([
            'academy_company_id' => $company->id,
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'type' => ShopProduct::TYPE_PHYSICAL,
            'name' => 'Produto Release',
            'slug' => 'produto-release-'.Str::random(6),
            'price' => 49.90,
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

        $this->actingAs($user)
            ->post(route('shopping.checkout.process'), [
                'payment_method' => 'pix',
                'shipping_method' => 'pickup',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('shop_orders', [
            'user_id' => $user->id,
            'status' => ShopOrder::STATUS_PAID,
        ]);
    }
}
