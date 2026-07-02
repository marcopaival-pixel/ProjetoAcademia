<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\User;
use App\Services\Payment\PaymentProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService extends BasePaymentGateway implements PaymentGatewayInterface
{
    protected $token;

    protected $baseUrl;

    protected string $webhookSecret = '';

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->token = $config['access_token'] ?? '';
        $this->webhookSecret = (string) ($config['webhook_secret'] ?? '');
        $this->baseUrl = ($config['environment'] ?? 'sandbox') === 'production'
            ? 'https://www.asaas.com/api/v3'
            : 'https://sandbox.asaas.com/api/v3';
    }

    public function getIdentifier(): string
    {
        return 'asaas';
    }

    public function createCheckout(User $user, float $amount, array $options = []): array
    {
        // 1. Get or Create Customer in Asaas
        $customerId = $this->getOrCreateCustomer($user);
        if (!$customerId) return ['ok' => false, 'error' => 'Falha ao criar cliente no Asaas'];

        // 2. Create Payment
        $payload = [
            'customer' => $customerId,
            'billingType' => 'UNDEFINED', // Allows customer to choose
            'value' => $amount,
            'dueDate' => now()->addDays(3)->format('Y-m-d'),
            'description' => $options['description'] ?? 'Pagamento via ProjetoAcademia',
            'externalReference' => $options['external_reference'] ?? null,
            'notificationDisabled' => false,
        ];

        $response = Http::withHeaders(['access_token' => $this->token])
            ->post($this->baseUrl . '/payments', $payload);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'ok' => true,
                'id' => $data['id'],
                'init_point' => $data['invoiceUrl'], // Asaas invoice URL acts as checkout
                'data' => $data
            ];
        }

        return ['ok' => false, 'error' => $response->json('errors.0.description') ?? 'Erro desconhecido no Asaas'];
    }

    public function createSubscription(User $user, $plan, array $options = []): array
    {
        $customerId = $this->getOrCreateCustomer($user);

        $baseAmount = $plan instanceof \App\Models\Plan
            ? (float) $plan->price
            : (float) ($options['amount'] ?? 0);
        $referralDiscount = (float) ($options['referral_discount'] ?? 0);
        $finalAmount = max(0, round($baseAmount - $referralDiscount, 2));

        $payload = [
            'customer' => $customerId,
            'billingType' => 'CREDIT_CARD',
            'value' => $finalAmount > 0 ? $finalAmount : $baseAmount,
            'nextDueDate' => now()->addMonth()->format('Y-m-d'),
            'cycle' => ($plan === 'yearly' || (is_string($plan) && $plan === 'yearly')) ? 'YEARLY' : 'MONTHLY',
            'description' => "Assinatura ProjetoAcademia - " . ($plan instanceof \App\Models\Plan ? $plan->name : $plan),
            'externalReference' => $options['external_reference'] ?? null,
        ];

        $response = Http::withHeaders(['access_token' => $this->token])
            ->post($this->baseUrl . '/subscriptions', $payload);

        if ($response->successful()) {
            $data = $response->json();
            $subId = $data['id'] ?? null;
            $invoiceUrl = $data['invoiceUrl'] ?? null;
            
            if (!$invoiceUrl && $subId) {
                $payResponse = Http::withHeaders(['access_token' => $this->token])
                    ->get($this->baseUrl . "/payments", ['subscription' => $subId]);
                if ($payResponse->successful()) {
                    $payments = $payResponse->json('data');
                    if (!empty($payments)) {
                        $invoiceUrl = $payments[0]['invoiceUrl'] ?? null;
                    }
                }
            }

            return [
                'ok' => true,
                'id' => $subId,
                'init_point' => $invoiceUrl,
                'data' => $data
            ];
        }

        return ['ok' => false, 'error' => $response->json('errors.0.description') ?? 'Erro ao criar assinatura no Asaas'];
    }

    public function cancelSubscription($gatewaySubscriptionId): bool
    {
        $response = Http::withHeaders(['access_token' => $this->token])
            ->delete($this->baseUrl . "/subscriptions/{$gatewaySubscriptionId}");

        return $response->successful();
    }

    public function fetchPayment(string $paymentId): array
    {
        $response = Http::withHeaders(['access_token' => $this->token])
            ->get($this->baseUrl . "/payments/{$paymentId}");

        if ($response->successful()) {
            return ['ok' => true, 'payment' => $response->json()];
        }

        return ['ok' => false, 'error' => 'Pagamento não encontrado'];
    }

    public function handleWebhook(Request $request): array
    {
        $start = microtime(true);
        $payload = $request->all();
        $event = $payload['event'] ?? '';

        try {
            if (!$this->validateSignature($request)) {
                $this->logWebhook($request, 401, 'Invalid Signature');
                return ['ok' => false, 'status' => 401];
            }

            // Asaas events: PAYMENT_RECEIVED, PAYMENT_CONFIRMED
            if ($event === 'PAYMENT_RECEIVED' || $event === 'PAYMENT_CONFIRMED') {
                $payment = $payload['payment'] ?? [];
                $externalRef = $payment['externalReference'] ?? '';
                
                // Extract User ID from externalReference if it follows "pa:USER_ID:PLAN" format 
                // or use it directly if it's "credits:ID"
                $userId = null;
                $reference = $externalRef;

                if (str_starts_with($externalRef, 'pa:')) {
                    $parts = explode(':', $externalRef);
                    $userId = $parts[1] ?? null;
                    $reference = $parts[2] ?? '';
                }

                if ($userId) {
                    $processor = app(PaymentProcessor::class);
                    $res = $processor->processApproved([
                        'user_id' => $userId,
                        'gateway' => 'asaas',
                        'gateway_id' => $payment['id'],
                        'amount' => (float) $payment['value'],
                        'reference' => $reference,
                        'payload' => $payload
                    ]);
                    
                    $this->logWebhook($request, 200, $res['message'], null, microtime(true) - $start);
                } else {
                    $this->logWebhook($request, 200, 'Ignored (No User ID)', null, microtime(true) - $start);
                }
            } elseif (str_starts_with($event, 'SUBSCRIPTION_')) {
                $this->syncSubscriptionEvent($payload);
                $this->logWebhook($request, 200, "Event {$event} subscription sync", null, microtime(true) - $start);
            } elseif (in_array($event, ['PAYMENT_REFUNDED', 'PAYMENT_DELETED', 'PAYMENT_CHARGEBACK'], true)) {
                $payment = $payload['payment'] ?? [];
                $gatewayId = $payment['id'] ?? null;

                if ($gatewayId) {
                    $paymentModel = \App\Models\Payment::where('gateway', 'asaas')
                        ->where('gateway_id', (string) $gatewayId)
                        ->first();

                    if ($paymentModel) {
                        $refundAmount = isset($payment['value']) ? (float) $payment['value'] : null;
                        app(\App\Services\PaymentRefundService::class)->processRefund(
                            $paymentModel,
                            $refundAmount,
                            'asaas_webhook'
                        );
                    }
                }

                $this->logWebhook($request, 200, "Event {$event} processed", null, microtime(true) - $start);
            } else {
                $this->logWebhook($request, 200, "Event {$event} ignored", null, microtime(true) - $start);
            }

            return ['ok' => true, 'status' => 200];
        } catch (\Exception $e) {
            $this->logWebhook($request, 500, 'Error', $e->getMessage(), microtime(true) - $start);
            return ['ok' => false, 'status' => 500];
        }
    }

    public function validateSignature(Request $request): bool
    {
        if ($this->webhookSecret === '') {
            if (app()->environment('production')) {
                Log::warning('[asaas_webhook] webhook_secret não configurado em produção — rejeitado.');

                return false;
            }

            Log::debug('[asaas_webhook] webhook_secret não configurado — validação ignorada (apenas dev).');

            return true;
        }

        $receivedToken = (string) $request->header('asaas-access-token', '');

        return hash_equals($this->webhookSecret, $receivedToken);
    }

    public function refund(string $paymentId, ?float $amount = null): bool
    {
        $payload = $amount ? ['value' => $amount] : [];
        $response = Http::withHeaders(['access_token' => $this->token])
            ->post($this->baseUrl . "/payments/{$paymentId}/refund", $payload);

        return $response->successful();
    }

    protected function syncSubscriptionEvent(array $payload): void
    {
        $event = $payload['event'] ?? '';
        $subscription = $payload['subscription'] ?? [];
        $gatewayId = $subscription['id'] ?? null;

        if (! $gatewayId) {
            return;
        }

        $externalRef = (string) ($subscription['externalReference'] ?? '');
        $userId = null;
        $planCode = 'monthly';

        if (str_starts_with($externalRef, 'pa:')) {
            $parts = explode(':', $externalRef);
            $userId = isset($parts[1]) ? (int) $parts[1] : null;
            $planCode = $parts[2] ?? 'monthly';
        }

        if (! $userId) {
            return;
        }

        $status = match ($event) {
            'SUBSCRIPTION_CREATED', 'SUBSCRIPTION_ACTIVATED', 'SUBSCRIPTION_RENEWED' => \App\Models\Subscription::STATUS_FIN_ATIVO,
            'SUBSCRIPTION_INACTIVATED', 'SUBSCRIPTION_DELETED' => \App\Models\Subscription::STATUS_FIN_CANCELADO,
            default => \App\Models\Subscription::STATUS_FIN_ATIVO,
        };

        $plan = \App\Models\Plan::where('name', $planCode)->first() ?? \App\Models\Plan::first();
        $isYearly = ($subscription['cycle'] ?? '') === 'YEARLY' || $planCode === 'yearly';

        $model = \App\Models\Subscription::updateOrCreate(
            ['gateway_type' => 'asaas', 'gateway_id' => (string) $gatewayId],
            [
                'user_id' => $userId,
                'plan_id' => $plan?->id ?? 1,
                'status' => $status,
                'start_date' => now(),
                'end_date' => $isYearly ? now()->addYear() : now()->addMonth(),
            ]
        );

        $user = User::find($userId);
        if ($user && in_array($status, [\App\Models\Subscription::STATUS_FIN_ATIVO, \App\Models\Subscription::STATUS_ACTIVE], true)) {
            $user->update([
                'is_premium' => true,
                'premium_expires_at' => $model->end_date,
            ]);
        } elseif ($user && $status === \App\Models\Subscription::STATUS_FIN_CANCELADO) {
            $user->update(['is_premium' => false]);
        }
    }

    protected function getOrCreateCustomer(User $user): ?string
    {
        // 1. Check if user already has an asaas_id (we should probably add this to the users table)
        // For now, let's look for customer by email
        $response = Http::withHeaders(['access_token' => $this->token])
            ->get($this->baseUrl . '/customers', ['email' => $user->email]);

        if ($response->successful() && !empty($response->json('data'))) {
            return $response->json('data.0.id');
        }

        // 2. Create if not found
        $response = Http::withHeaders(['access_token' => $this->token])
            ->post($this->baseUrl . '/customers', [
                'name' => $user->name,
                'email' => $user->email,
                'cpfCnpj' => $user->cpf ?? '', // Assuming user has CPF
            ]);

        return $response->json('id');
    }
}
