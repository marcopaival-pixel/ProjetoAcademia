<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\Clinic;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
