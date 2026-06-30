<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\CreditoCompra;
use App\Models\AiCreditPackage;
use App\Models\AiCreditTransaction;
use App\Services\AiCreditService;
use App\Services\CommissionService;
use App\Services\FinancialLogService;
use App\Services\Shop\ShopPaymentFulfillmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
            $reference = $data['reference'] ?? '';

            $alreadyProcessed = Payment::query()
                ->where('gateway', $gateway)
                ->where('gateway_id', $gatewayId)
                ->where('status', 'paid')
                ->exists();

            if ($alreadyProcessed) {
                return ['ok' => true, 'message' => 'Pagamento já processado.'];
            }

            $user = User::findOrFail($userId);

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
            } elseif (str_starts_with($reference, 'shop:')) {
                $orderId = (int) str_replace('shop:', '', $reference);
                app(ShopPaymentFulfillmentService::class)
                    ->markOrderPaidFromGateway($orderId, $gatewayId, $gateway, $amount);
            } else {
                $subscription = $this->processSubscription($user, $reference, $gateway, $gatewayId);
            }

            if ($subscription !== null) {
                $payment->update(['subscription_id' => $subscription->id]);
                app(CommissionService::class)->recordOnPayment($user, $payment, $subscription);
            }

            // 4. Financial Log
            if (! str_starts_with($reference, 'shop:')) {
                FinancialLogService::log([
                    'user_id' => $userId,
                    'action' => 'PAYMENT_RECEIVED',
                    'amount' => $amount,
                    'transaction_id' => $gatewayId,
                    'origin' => $gateway,
                    'payload' => ['reference' => $reference]
                ]);
            }

            return ['ok' => true, 'message' => 'Pagamento processado com sucesso'];
        });
    }

    protected function processSubscription(User $user, string $planCode, string $gateway, string $gatewayId): Subscription
    {
        $plan = Plan::where('name', $planCode)->first() ?? Plan::first();
        
        $lookup = $gatewayId !== ''
            ? ['gateway_type' => $gateway, 'gateway_id' => $gatewayId]
            : ['user_id' => $user->id, 'gateway_type' => $gateway];

        $subscription = Subscription::updateOrCreate(
            $lookup,
            [
                'user_id' => $user->id,
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

        return $subscription->fresh(['plan']);
    }

    public function applyAiCreditsPurchase(User $user, int $packageId, string $gatewayId, string $gateway = 'mercadopago'): void
    {
        $this->processAiCredits($user, $packageId, $gatewayId, $gateway);
    }

    public function applyGeneralCreditsPurchase(User $user, int $compraId, string $gatewayId): void
    {
        $this->processGeneralCredits($user, $compraId, $gatewayId);
    }

    protected function processAiCredits(User $user, int $packageId, string $gatewayId, string $gateway)
    {
        $alreadyCredited = AiCreditTransaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'purchase')
            ->where('reference_id', $gatewayId)
            ->exists();

        if ($alreadyCredited) {
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
        if (! $compra || $compra->status !== 'PENDENTE' || (int) $compra->user_id !== (int) $user->id) {
            return;
        }

        $compra->update(['status' => 'PAGO', 'payment_id' => $gatewayId]);
        $user->increment('creditos', $compra->quantidade);

        if (Schema::hasColumn('users', 'ai_credits')) {
            $user->increment('ai_credits', $compra->quantidade);
        }
    }

}
