<?php

namespace App\Services\Payment;

use App\Models\Commission;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionClawbackService
{
    /**
     * Reverte comissões ligadas a um pagamento reembolsado.
     * Idempotente: comissões já canceladas são ignoradas.
     */
    public function reverseForPayment(Payment $payment, string $reason = 'refund'): void
    {
        DB::transaction(function () use ($payment, $reason) {
            $commissions = Commission::query()
                ->where('payment_id', $payment->id)
                ->where('status', '!=', Commission::STATUS_CANCELADO)
                ->where('commission_amount', '>', 0)
                ->lockForUpdate()
                ->get();

            if ($commissions->isEmpty()) {
                return;
            }

            $alreadyClawedBack = Commission::query()
                ->where('payment_id', $payment->id)
                ->where('commission_amount', '<', 0)
                ->exists();

            foreach ($commissions as $commission) {
                if ($commission->status === Commission::STATUS_PAGO && ! $alreadyClawedBack) {
                    Commission::create([
                        'representative_id' => $commission->representative_id,
                        'user_id' => $commission->user_id,
                        'payment_id' => $payment->id,
                        'subscription_id' => $commission->subscription_id,
                        'base_amount' => $commission->base_amount,
                        'commission_rate' => $commission->commission_rate,
                        'commission_amount' => -abs((float) $commission->commission_amount),
                        'status' => Commission::STATUS_DISPONIVEL,
                        'available_at' => now(),
                        'notes' => "Clawback automático ({$reason}) — estorno comissão #{$commission->id} já paga ao representante",
                    ]);
                    $alreadyClawedBack = true;
                }

                $commission->update([
                    'status' => Commission::STATUS_CANCELADO,
                    'notes' => trim(($commission->notes ?? '')." Cancelada: {$reason}."),
                ]);
            }

            Log::info('Clawback de comissões processado', [
                'payment_id' => $payment->id,
                'gateway_id' => $payment->gateway_id,
                'reason' => $reason,
                'count' => $commissions->count(),
            ]);
        });
    }
}
