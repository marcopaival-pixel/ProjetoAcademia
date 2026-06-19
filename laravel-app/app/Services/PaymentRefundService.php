<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Support\SubscriptionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentRefundService
{
    /**
     * Processa estorno de pagamento (idempotente). Cancela comissões e revoga benefícios.
     */
    public function processRefund(Payment $payment, ?float $refundedAmount = null, string $origin = 'system'): bool
    {
        if ($payment->status === 'refunded') {
            return true;
        }

        return (bool) DB::transaction(function () use ($payment, $refundedAmount, $origin) {
            $amount = $refundedAmount ?? (float) $payment->amount;

            $payment->update([
                'status' => 'refunded',
                'payload' => array_merge($payment->payload ?? [], [
                    'refunded_at' => now()->toIso8601String(),
                    'refund_origin' => $origin,
                    'refunded_amount' => $amount,
                ]),
            ]);

            app(CommissionClawbackService::class)->processForPayment($payment);

            $subscription = $payment->subscription_id
                ? Subscription::find($payment->subscription_id)
                : Subscription::where('user_id', $payment->user_id)->latest('id')->first();

            if ($subscription) {
                app(SubscriptionService::class)->refund($subscription, $amount, true);
            }

            $user = User::find($payment->user_id);
            if ($user) {
                $hasOtherActive = Subscription::where('user_id', $user->id)
                    ->when($subscription, fn ($q) => $q->where('id', '!=', $subscription->id))
                    ->whereCanonicalStatus(...SubscriptionStatus::premiumEligible())
                    ->exists();

                if (! $hasOtherActive) {
                    $user->update([
                        'is_premium' => false,
                        'premium_expires_at' => null,
                    ]);
                }
            }

            FinancialLogService::log([
                'user_id' => $payment->user_id,
                'action' => 'REFUND',
                'amount' => $amount,
                'transaction_id' => $payment->gateway_id,
                'origin' => $origin,
                'payload' => [
                    'payment_id' => $payment->id,
                    'gateway' => $payment->gateway,
                ],
            ]);

            Log::info('[PaymentRefund] Estorno processado.', [
                'payment_id' => $payment->id,
                'gateway_id' => $payment->gateway_id,
                'amount' => $amount,
                'origin' => $origin,
            ]);

            app(CommissionMetricsService::class)->clearCache();
            app(ExecutiveDashboardService::class)->clearCache();

            return true;
        });
    }

    /**
     * Solicita estorno no gateway e processa localmente se bem-sucedido.
     */
    public function initiateGatewayRefund(Payment $payment, ?float $amount = null): bool
    {
        $manager = app(\App\Services\Payment\PaymentGatewayManager::class);
        $gateway = $manager->driver($payment->gateway);

        if (! $gateway->refund($payment->gateway_id, $amount)) {
            return false;
        }

        return $this->processRefund($payment, $amount, $payment->gateway.'_admin');
    }
}
