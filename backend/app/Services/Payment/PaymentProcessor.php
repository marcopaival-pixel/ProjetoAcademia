<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\CreditoCompra;
use App\Models\AiCreditPackage;
use App\Models\Commission;
use App\Services\FinancialLogService;
use App\Services\AiCreditService;
use App\Services\Payment\CommissionClawbackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentProcessor
{
    /**
     * Process an approved payment.
     */
    public function processApproved(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $userId = $data['user_id'];
            $gateway = $data['gateway'];
            $gatewayId = $data['gateway_id'];
            $amount = $data['amount'];
            $reference = $data['reference'] ?? ''; // e.g., "monthly", "yearly", "credits:1"
            
            $user = User::findOrFail($userId);
            
            // 1. Create or Update Payment record
            $payment = Payment::updateOrCreate(
                ['gateway' => $gateway, 'gateway_id' => $gatewayId],
                [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'fee_amount' => $data['fee_amount'] ?? 0,
                    'net_amount' => $amount - ($data['fee_amount'] ?? 0),
                    'currency' => $data['currency'] ?? 'BRL',
                    'status' => 'paid',
                    'payload' => $data['payload'] ?? [],
                ]
            );

            $subscription = null;

            // 2. Determine Action
            if (str_starts_with($reference, 'ai_credits:')) {
                $packageId = (int) str_replace('ai_credits:', '', $reference);
                $this->processAiCredits($user, $packageId, $gatewayId, $gateway);
            } elseif (str_starts_with($reference, 'credits:')) {
                $compraId = (int) str_replace('credits:', '', $reference);
                $this->processGeneralCredits($user, $compraId, $gatewayId);
            } else {
                $subscription = $this->processSubscription($user, $reference, $gateway, $gatewayId);
            }

            if ($subscription !== null) {
                $payment->update(['subscription_id' => $subscription->id]);
            }

            // 3. Process Commission
            $this->processCommission($user, $payment, $reference);

            // 4. Financial Log
            FinancialLogService::log([
                'user_id' => $userId,
                'action' => 'PAYMENT_RECEIVED',
                'amount' => $amount,
                'transaction_id' => $gatewayId,
                'origin' => $gateway,
                'payload' => ['reference' => $reference]
            ]);

            return ['ok' => true, 'message' => 'Pagamento processado com sucesso'];
        });
    }

    /**
     * Processa reembolso/chargeback (idempotente).
     *
     * @param  array{gateway: string, gateway_id: string, reason?: string, payload?: array}  $data
     */
    public function processRefund(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::query()
                ->where('gateway', $data['gateway'])
                ->where('gateway_id', $data['gateway_id'])
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                return ['ok' => true, 'message' => 'Pagamento não encontrado (ignorado)'];
            }

            if ($payment->status === 'refunded') {
                return ['ok' => true, 'message' => 'Reembolso já processado (idempotente)'];
            }

            $reason = $data['reason'] ?? 'refund';

            $payment->update([
                'status' => 'refunded',
                'payload' => array_merge($payment->payload ?? [], [
                    'refund' => $data['payload'] ?? [],
                    'refund_reason' => $reason,
                    'refunded_at' => now()->toIso8601String(),
                ]),
            ]);

            app(CommissionClawbackService::class)->reverseForPayment($payment, $reason);

            FinancialLogService::log([
                'user_id' => $payment->user_id,
                'action' => 'PAYMENT_REFUNDED',
                'amount' => $payment->amount,
                'transaction_id' => $payment->gateway_id,
                'origin' => $payment->gateway,
                'payload' => ['reason' => $reason],
            ]);

            return ['ok' => true, 'message' => 'Reembolso processado'];
        });
    }

    protected function processSubscription(User $user, string $planCode, string $gateway, string $gatewayId)
    {
        $plan = Plan::where('name', $planCode)->first() ?? Plan::first();
        
        $subscription = Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_FIN_ATIVO,
                'gateway_id' => $gatewayId,
                'gateway_type' => $gateway,
                'start_date' => now(),
                'end_date' => $planCode === 'yearly' ? now()->addYear() : now()->addMonth(),
            ]
        );

        $user->update([
            'is_premium' => true,
            'premium_expires_at' => $subscription->end_date
        ]);

        return $subscription;
    }

    protected function processAiCredits(User $user, int $packageId, string $gatewayId, string $gateway)
    {
        $existing = \App\Models\AiCreditTransaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'purchase')
            ->where('reference_id', $gatewayId)
            ->exists();

        if ($existing) {
            Log::info('Webhook IA ignorado (idempotente)', [
                'user_id' => $user->id,
                'gateway_id' => $gatewayId,
            ]);

            return;
        }

        $package = AiCreditPackage::find($packageId);
        if ($package) {
            app(AiCreditService::class)->addCredits(
                $user,
                $package->credits,
                'purchase',
                "Compra de créditos IA: {$package->name} (Gateway: {$gateway})",
                $gatewayId
            );
        }
    }

    protected function processGeneralCredits(User $user, int $compraId, string $gatewayId)
    {
        $compra = CreditoCompra::find($compraId);
        if ($compra && $compra->status === 'PENDENTE') {
            $compra->update(['status' => 'PAGO', 'gateway_id' => $gatewayId]);
            $user->increment('creditos', $compra->quantidade);
            
            // Também adiciona ao novo sistema de créditos de IA se aplicável
            app(AiCreditService::class)->addCredits(
                $user, 
                $compra->quantidade, 
                'purchase', 
                "Compra de créditos gerais convertidos para IA"
            );
        }
    }

    protected function processCommission(User $user, Payment $payment, string $reference = '')
    {
        if ($reference !== '' && (str_starts_with($reference, 'ai_credits:') || str_starts_with($reference, 'credits:'))) {
            return;
        }

        $representativeId = $user->representative_id;
        if (!$representativeId) return;

        $rate = (float) config('projeto.default_commission_rate', 10.00);
        $commissionAmount = ($payment->amount * $rate) / 100;

        Commission::create([
            'representative_id' => $representativeId,
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'base_amount' => $payment->amount,
            'commission_rate' => $rate,
            'commission_amount' => $commissionAmount,
            'status' => Commission::STATUS_PENDENTE,
            'available_at' => now()->addDays(7),
        ]);
    }
}
