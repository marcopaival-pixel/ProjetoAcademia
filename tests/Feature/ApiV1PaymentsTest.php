<?php

namespace Tests\Feature;

use App\Models\PaymentSetting;
use App\Models\User;
use App\Support\PaymentGatewayRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1PaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_payments_status_requires_authentication(): void
    {
        $this->getJson('/api/v1/payments/status')->assertUnauthorized();
    }

    public function test_payments_status_returns_implemented_gateways_without_secrets(): void
    {
        PaymentSetting::create([
            'gateway' => 'mercadopago',
            'status' => 'active',
            'environment' => 'sandbox',
            'access_token' => 'test-token-secret',
            'enable_pix' => true,
            'enable_credit_card' => true,
            'enable_boleto' => false,
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/payments/status')->assertOk();

        $response->assertJsonPath('active_gateway', 'mercadopago');
        $response->assertJsonPath('methods.pix', true);
        $response->assertJsonPath('implemented', PaymentGatewayRegistry::IMPLEMENTED);
        $response->assertJsonMissing(['access_token', 'client_secret', 'webhook_secret']);
    }
}
