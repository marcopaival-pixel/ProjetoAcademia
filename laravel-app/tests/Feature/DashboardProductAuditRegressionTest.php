<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\ProfessionalPatient;
use App\Models\Subscription;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class DashboardProductAuditRegressionTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_student_dashboard_displays_real_subscription_and_training_totals(): void
    {
        $plan = $this->createPlan('Plano Pro Teste', 'student', 49.90);
        $user = $this->userWithRole('aluno');

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_FIN_ATIVO,
            'payment_method' => 'pix',
            'start_date' => now()->subMonth(),
            'next_billing_date' => now()->addMonth()->toDateString(),
        ]);

        Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'gateway' => 'test',
            'gateway_id' => 'pay-audit-'.uniqid(),
            'amount' => 49.90,
            'status' => 'paid',
        ]);

        TrainingPlan::create([
            'user_id' => $user->id,
            'name' => 'Treino A',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'aluno'])
            ->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('Plano Pro Teste', false)
            ->assertSee('active', false)
            ->assertSee('R$ 49,90', false);

        $this->assertSame(1, $response->viewData('trainingCount'));
        $this->assertSame('Plano Pro Teste', $response->viewData('subscriptionSummary')['plan_name']);
        $this->assertSame('active', $response->viewData('subscriptionSummary')['status']);
    }

    public function test_professional_dashboard_rejects_active_patient_without_link(): void
    {
        $professional = $this->userWithRole('professional');
        $unlinkedPatient = $this->userWithRole('aluno');

        $this->actingAs($professional)
            ->withSession([
                'active_role' => 'professional',
                'active_patient_id' => $unlinkedPatient->id,
            ])
            ->get(route('professional.dashboard'))
            ->assertNotFound();
    }

    public function test_professional_dashboard_uses_only_own_records_for_active_patient_stats(): void
    {
        $professional = $this->userWithRole('professional');
        $otherProfessional = $this->userWithRole('professional');
        $patient = $this->userWithRole('aluno');

        ProfessionalPatient::create([
            'profissional_id' => $professional->id,
            'user_id' => $patient->id,
            'status' => 'Sim',
            'data_cadastro' => now(),
        ]);

        TrainingPlan::create([
            'user_id' => $patient->id,
            'professional_id' => $otherProfessional->id,
            'name' => 'Treino de outro profissional',
            'is_active' => true,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $ownTraining = TrainingPlan::create([
            'user_id' => $patient->id,
            'professional_id' => $professional->id,
            'name' => 'Treino do profissional correto',
            'is_active' => true,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($professional)
            ->withSession([
                'active_role' => 'professional',
                'active_patient_id' => $patient->id,
            ])
            ->get(route('professional.dashboard'));

        $response->assertOk();
        $this->assertSame(
            $ownTraining->created_at->format('d/m/Y'),
            $response->viewData('activePatientStats')['last_training']
        );
    }

    public function test_subscription_payment_method_ignores_raw_card_number_and_cvv(): void
    {
        $plan = $this->createPlan('Free', 'student', 0);
        $user = $this->userWithRole('aluno');

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'inactive',
            'payment_method' => 'pix',
            'start_date' => now(),
        ]);

        $this->actingAs($user)
            ->withSession(['active_role' => 'aluno'])
            ->post(route('patient.subscription.update-payment'), [
                'method' => 'card',
                'card_number' => '4111111111111111',
                'card_cvv' => '123',
            ])
            ->assertRedirect();

        $subscription->refresh();

        $this->assertSame('card', $subscription->payment_method);
        $this->assertNull($subscription->card_last_four);
        $this->assertNull($subscription->card_brand);
        $this->assertNull($subscription->card_expiry);
    }

    public function test_checkout_and_subscription_views_do_not_render_raw_card_or_fake_pix_fields(): void
    {
        $plan = $this->createPlan('Plano Checkout Seguro', 'student', 29.90);
        $user = $this->userWithRole('aluno');

        $this->get(route('checkout.index', $plan))
            ->assertOk()
            ->assertDontSee('name="card_number"', false)
            ->assertDontSee('name="card_cvv"', false)
            ->assertDontSee('PIX COPIA E COLA', false);

        $this->actingAs($user)
            ->withSession(['active_role' => 'aluno'])
            ->get(route('patient.subscription.index'))
            ->assertOk()
            ->assertDontSee('name="card_number"', false)
            ->assertDontSee('name="card_cvv"', false)
            ->assertDontSee('PIX COPIA E COLA', false);
    }

    private function createPlan(string $name, string $type, float $price): Plan
    {
        $plan = Plan::create([
            'name' => $name,
            'type' => $type,
            'price' => $price,
            'description' => 'Plano criado para teste automatizado',
        ]);

        $plan->forceFill(['is_active' => true])->save();

        return $plan;
    }
}
