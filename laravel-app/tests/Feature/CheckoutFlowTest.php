<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_process_activates_free_plan_for_authenticated_user(): void
    {
        AdminSetting::set('pagamento_ativo', 'false');

        $plan = Plan::create([
            'name' => 'Plano Teste Free',
            'description' => 'Teste automatizado',
            'type' => 'student',
            'price' => 0,
        ]);
        $plan->forceFill(['is_active' => true])->save();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'registration_approval_status' => 'approved',
        ]);

        $response = $this->actingAs($user)->postJson(route('checkout.process'), [
            'plan_id' => $plan->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Plano ativado com sucesso!');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_FIN_ATIVO,
        ]);
    }

    public function test_checkout_process_registers_guest_and_activates_free_plan(): void
    {
        AdminSetting::set('pagamento_ativo', 'false');

        $plan = Plan::create([
            'name' => 'Plano Guest',
            'type' => 'student',
            'price' => 0,
        ]);
        $plan->forceFill(['is_active' => true])->save();

        $email = 'checkout-guest-'.uniqid().'@example.test';

        $this->postJson(route('checkout.process'), [
            'plan_id' => $plan->id,
            'name' => 'Guest Checkout',
            'email' => $email,
            'password' => 'SenhaSegura123',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    public function test_checkout_index_returns_success_for_active_plan(): void
    {
        $plan = Plan::create([
            'name' => 'Plano Ativo',
            'type' => 'student',
            'price' => 0,
        ]);
        $plan->forceFill(['is_active' => true])->save();

        $this->get(route('checkout.index', $plan->id))
            ->assertOk();
    }
}
