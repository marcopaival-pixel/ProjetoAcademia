<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Payment;
use App\Models\RepresentativeAudit;
use Illuminate\Support\Facades\Log;

class CommissionClawbackService
{
    /**
     * Estorna comissões ligadas ao pagamento; gera clawback automático se já estiver paga ao representante.
     */
    public function processForPayment(Payment $payment): void
    {
        Commission::where('payment_id', $payment->id)
            ->whereNotIn('status', [Commission::STATUS_CANCELADO, Commission::STATUS_CLAWBACK])
            ->get()
            ->each(fn (Commission $commission) => $this->clawbackCommission($commission, $payment));
    }

    private function clawbackCommission(Commission $commission, Payment $payment): void
    {
        if ($commission->status === Commission::STATUS_PAGO) {
            $clawback = Commission::create([
                'representative_id' => $commission->representative_id,
                'user_id' => $commission->user_id,
                'clinic_id' => $commission->clinic_id,
                'payment_id' => $payment->id,
                'subscription_id' => $commission->subscription_id,
                'base_amount' => -abs((float) $commission->base_amount),
                'commission_rate' => $commission->commission_rate,
                'commission_type' => $commission->commission_type,
                'commission_amount' => -abs((float) $commission->commission_amount),
                'status' => Commission::STATUS_CLAWBACK,
                'notes' => "Clawback automático da comissão #{$commission->id} (estorno pagamento #{$payment->id})",
            ]);

            RepresentativeAudit::create([
                'user_id' => $commission->representative_id,
                'action' => 'commission_clawback',
                'entity_type' => Commission::class,
                'entity_id' => $clawback->id,
                'new_values' => [
                    'original_commission_id' => $commission->id,
                    'amount' => $clawback->commission_amount,
                    'payment_id' => $payment->id,
                ],
            ]);

            Log::info('[CommissionClawback] Clawback automático gerado.', [
                'commission_id' => $commission->id,
                'clawback_id' => $clawback->id,
                'amount' => $clawback->commission_amount,
            ]);
        }

        $commission->update([
            'status' => Commission::STATUS_CANCELADO,
            'notes' => trim(($commission->notes ?? '').' [Cancelada: estorno do pagamento]'),
        ]);

        RepresentativeAudit::create([
            'user_id' => $commission->representative_id,
            'action' => 'commission_cancelled_refund',
            'entity_type' => Commission::class,
            'entity_id' => $commission->id,
            'new_values' => ['status' => Commission::STATUS_CANCELADO],
        ]);

        app(CommissionMetricsService::class)->clearCache();
    }
}
