<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\Clinic;
use App\Models\Commission;
use App\Models\Plan;
use App\Models\ReferralCode;
use App\Models\RepresentativeProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ReferralCommissionCheckoutTest extends TestCase
{
    use RefreshDatabase, SeedsRbacForTests;

    public function test_free_checkout_with_referral_code_records_commission(): void
    {
        AdminSetting::set('pagamento_ativo', 'false');

        $plan = Plan::create([
            'name' => 'Plano Pro Referral',
            'type' => 'student',
            'price' => 100,
        ]);
        $plan->forceFill(['is_active' => true])->save();

        $clinic = Clinic::create([
            'name' => 'Clínica Referral',
            'slug' => 'clinica-referral',
            'status' => 'active',
        ]);

        $representative = $this->userWithRole('representative');
        RepresentativeProfile::create([
            'user_id' => $representative->id,
            'code' => 'REP-PROFILE-FALLBACK',
            'commission_rate' => 10,
            'max_discount_rate' => 5,
        ]);

        ReferralCode::withoutGlobalScopes()->create([
            'code' => 'REP-CHECKOUT-TEST',
            'representative_id' => $representative->id,
            'status' => ReferralCode::STATUS_DISPONIVEL,
        ]);

        $buyer = User::factory()->create([
            'clinic_id' => $clinic->id,
            'email_verified_at' => now(),
            'registration_approval_status' => 'approved',
        ]);

        $this->actingAs($buyer)->postJson(route('checkout.process'), [
            'plan_id' => $plan->id,
            'referral_code' => 'REP-CHECKOUT-TEST',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('commissions', [
            'representative_id' => $representative->id,
            'user_id' => $buyer->id,
            'commission_rate' => 10,
            'commission_amount' => 10,
            'status' => Commission::STATUS_AGUARDANDO_PAGAMENTO,
        ]);

        $clinic->refresh();
        $this->assertSame($representative->id, $clinic->representative_id);
        $this->assertDatabaseHas('referral_codes', [
            'code' => 'REP-CHECKOUT-TEST',
            'status' => ReferralCode::STATUS_UTILIZADO,
            'clinic_id' => $clinic->id,
        ]);
    }

    public function test_referral_codes_are_scoped_to_authenticated_representative(): void
    {
        $repA = $this->userWithRole('representative');
        $repB = $this->userWithRole('representative');

        $codeA = ReferralCode::withoutGlobalScopes()->create([
            'code' => 'REP-A-0001',
            'representative_id' => $repA->id,
            'status' => ReferralCode::STATUS_DISPONIVEL,
        ]);

        $codeB = ReferralCode::withoutGlobalScopes()->create([
            'code' => 'REP-B-0001',
            'representative_id' => $repB->id,
            'status' => ReferralCode::STATUS_DISPONIVEL,
        ]);

        $this->actingAs($repA);

        $visibleIds = ReferralCode::pluck('id')->all();

        $this->assertContains($codeA->id, $visibleIds);
        $this->assertNotContains($codeB->id, $visibleIds);
    }
}
