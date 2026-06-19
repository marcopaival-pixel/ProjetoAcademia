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
            $commission->update([
                'base_amount' => -abs((float) $commission->base_amount),
                'commission_amount' => -abs((float) $commission->commission_amount),
                'status' => Commission::STATUS_CLAWBACK,
                'notes' => trim(($commission->notes ?? '')." [Clawback automático por estorno do pagamento #{$payment->id}]"),
            ]);

            RepresentativeAudit::create([
                'user_id' => $commission->representative_id,
                'action' => 'commission_clawback',
                'entity_type' => Commission::class,
                'entity_id' => $commission->id,
                'new_values' => [
                    'payment_id' => $payment->id,
                    'amount' => $commission->commission_amount,
                ],
            ]);

            Log::info('[CommissionClawback] Clawback automático aplicado na comissão original.', [
                'commission_id' => $commission->id,
                'amount' => $commission->commission_amount,
            ]);

            app(CommissionMetricsService::class)->clearCache();

            return;
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
