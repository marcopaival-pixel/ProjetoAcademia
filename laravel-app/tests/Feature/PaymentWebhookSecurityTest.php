<?php

namespace Tests\Feature;

use App\Services\Payment\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PaymentWebhookSecurityTest extends TestCase
{
    private function useProductionEnvironment(): void
    {
        $this->app['env'] = 'production';
    }

    public function test_mp_webhook_requires_secret_in_production(): void
    {
        $this->useProductionEnvironment();
        Config::set('projeto.mp_access_token', 'test-token');
        Config::set('projeto.mp_webhook_secret', '');

        $this->post('/mp/webhook', [], ['Content-Type' => 'application/json'])
            ->assertStatus(503);
    }

    public function test_asaas_rejects_webhook_without_secret_in_production(): void
    {
        $this->useProductionEnvironment();

        $service = new AsaasService(['access_token' => 'token', 'webhook_secret' => '']);
        $request = Request::create('/payment/webhook/asaas', 'POST', [], [], [], [
            'HTTP_asaas-access-token' => 'any',
        ]);

        $this->assertFalse($service->validateSignature($request));
    }

    public function test_asaas_accepts_matching_secret(): void
    {
        $service = new AsaasService(['access_token' => 'token', 'webhook_secret' => 'whsec-test']);
        $request = Request::create('/payment/webhook/asaas', 'POST', [], [], [], [
            'HTTP_asaas-access-token' => 'whsec-test',
        ]);

        $this->assertTrue($service->validateSignature($request));
    }

    public function test_mercadopago_rejects_webhook_without_secret_in_production(): void
    {
        $this->useProductionEnvironment();

        $service = new \App\Services\MercadoPagoService([
            'access_token' => 'token',
            'webhook_secret' => '',
        ]);
        $request = Request::create('/payment/webhook/mercadopago', 'POST');

        $this->assertFalse($service->validateSignature($request));
    }
}
