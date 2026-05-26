<?php

namespace Tests\Feature;

use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MercadoPagoWebhookFlowTest extends TestCase
{
    private function mpSignatureHeaders(string $secret, string $dataId, string $requestId = 'req-test-1'): array
    {
        $ts = (string) time();
        $manifest = "id:{$dataId};request-id:{$requestId};ts:{$ts}";
        $v1 = hash_hmac('sha256', $manifest, $secret);

        return [
            'x-signature' => "ts={$ts};v1={$v1}",
            'x-request-id' => $requestId,
        ];
    }

    public function test_mp_webhook_rejects_invalid_signature_when_secret_configured(): void
    {
        Config::set('projeto.mp_access_token', 'test-token');
        Config::set('projeto.mp_webhook_secret', 'whsec-test');

        $payload = json_encode(['type' => 'payment', 'data' => ['id' => '999']]);

        $this->call(
            'POST',
            '/mp/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_x-signature' => 'ts=1;v1=invalid',
                'HTTP_x-request-id' => 'req-bad',
            ],
            $payload
        )->assertStatus(401);
    }

    public function test_mp_webhook_processes_payment_with_valid_signature(): void
    {
        $secret = 'whsec-flow-test';
        $paymentId = '123456789';
        Config::set('projeto.mp_access_token', 'test-token');
        Config::set('projeto.mp_webhook_secret', $secret);

        $this->mock(MercadoPagoService::class, function ($mock) use ($paymentId) {
            $mock->shouldReceive('fetchPayment')
                ->once()
                ->with($paymentId)
                ->andReturn(['ok' => true, 'payment' => ['id' => $paymentId, 'status' => 'approved']]);
            $mock->shouldReceive('tryCreditPremium')
                ->once()
                ->andReturn(['ok' => true, 'message' => 'credited']);
        });

        $payload = json_encode(['type' => 'payment', 'data' => ['id' => $paymentId]]);
        $headers = $this->mpSignatureHeaders($secret, $paymentId);

        $this->call(
            'POST',
            '/mp/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_x-signature' => $headers['x-signature'],
                'HTTP_x-request-id' => $headers['x-request-id'],
            ],
            $payload
        )->assertStatus(200)
            ->assertSee('ok');
    }

    public function test_mp_webhook_returns_ok_when_no_payment_id_in_payload(): void
    {
        Config::set('projeto.mp_access_token', 'test-token');
        Config::set('projeto.mp_webhook_secret', '');

        $this->post('/mp/webhook', [], ['Content-Type' => 'application/json'])
            ->assertStatus(200)
            ->assertSee('ok');
    }
}
