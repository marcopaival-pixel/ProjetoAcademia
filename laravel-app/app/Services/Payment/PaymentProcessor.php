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

            // 2. Determine Action
            if (str_starts_with($reference, 'ai_credits:')) {
                $packageId = (int) str_replace('ai_credits:', '', $reference);
                $this->processAiCredits($user, $packageId, $gatewayId);
            } elseif (str_starts_with($reference, 'credits:')) {
                $compraId = (int) str_replace('credits:', '', $reference);
                $this->processGeneralCredits($user, $compraId, $gatewayId);
            } else {
                $this->processSubscription($user, $reference, $gateway, $gatewayId);
            }

            // 3. Process Commission
            $this->processCommission($user, $payment);

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

    protected function processAiCredits(User $user, int $packageId, string $gatewayId)
    {
        $package = AiCreditPackage::find($packageId);
        if ($package) {
            app(AiCreditService::class)->addCredits($user, $package->credits, 'purchase', [
                'package_id' => $packageId,
                'gateway_id' => $gatewayId
            ]);
        }
    }

    protected function processGeneralCredits(User $user, int $compraId, string $gatewayId)
    {
        $compra = CreditoCompra::find($compraId);
        if ($compra && $compra->status === 'PENDENTE') {
            $compra->update(['status' => 'PAGO', 'gateway_id' => $gatewayId]);
            $user->increment('creditos', $compra->quantidade);
            
            if (\Schema::hasColumn('users', 'ai_credits')) {
                $user->increment('ai_credits', $compra->quantidade);
            }
        }
    }

    protected function processCommission(User $user, Payment $payment)
    {
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
