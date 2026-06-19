<?php

namespace App\Services;

use App\Models\CreditoCompra;
use App\Models\FinancialLog;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaymentReconciliationService
{
    /**
     * Gera relatório de divergências entre pagamentos, compras de crédito e logs financeiros.
     */
    public function analyze(int $staleDays = 7, ?Carbon $since = null): array
    {
        $since = $since ?? now()->subDays(30);
        $paidStatuses = ['paid', 'approved', 'ATIVO', \App\Models\Subscription::STATUS_FIN_ATIVO];

        $stalePendingCredits = CreditoCompra::query()
            ->where('status', 'PENDENTE')
            ->where('created_at', '<', now()->subDays($staleDays))
            ->count();

        $paidCreditsWithoutPayment = 0;
        if (Schema::hasTable('creditos_compras')) {
            $paidCreditsWithoutPayment = CreditoCompra::query()
                ->where('status', 'PAGO')
                ->where(function ($q) {
                    $q->whereNull('payment_id')->orWhere('payment_id', '');
                })
                ->count();
        }

        $paymentsWithoutLog = 0;
        if (Schema::hasTable('payments') && Schema::hasTable('financial_logs')) {
            $paymentsWithoutLog = Payment::query()
                ->whereIn('status', $paidStatuses)
                ->where('created_at', '>=', $since)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('financial_logs')
                        ->whereColumn('financial_logs.transaction_id', 'payments.gateway_id')
                        ->whereIn('financial_logs.action', ['PAYMENT_RECEIVED', 'AI_CREDITS_PURCHASED']);
                })
                ->count();
        }

        $paymentsTotal = (float) Payment::query()
            ->whereIn('status', $paidStatuses)
            ->where('created_at', '>=', $since)
            ->sum('amount');

        $logsTotal = 0.0;
        if (Schema::hasTable('financial_logs')) {
            $receivedTotal = (float) FinancialLog::query()
                ->whereIn('action', ['PAYMENT_RECEIVED', 'AI_CREDITS_PURCHASED'])
                ->where('created_at', '>=', $since)
                ->sum('amount');

            $refundedTotal = (float) FinancialLog::query()
                ->whereIn('action', ['PAYMENT_REFUNDED', 'REFUND'])
                ->where('created_at', '>=', $since)
                ->sum('amount');

            $logsTotal = $receivedTotal - $refundedTotal;
        }

        $legacyMpTotal = 0.0;
        if (Schema::hasTable('mercadopago_payment_credits')) {
            $legacyMpTotal = (float) DB::table('mercadopago_payment_credits')
                ->where('created_at', '>=', $since)
                ->sum('transaction_amount');
        }

        $divergence = abs($paymentsTotal - $logsTotal);

        return [
            'period_since' => $since->toIso8601String(),
            'stale_pending_credit_purchases' => $stalePendingCredits,
            'paid_credits_without_payment_id' => $paidCreditsWithoutPayment,
            'paid_payments_without_financial_log' => $paymentsWithoutLog,
            'payments_total' => round($paymentsTotal, 2),
            'financial_logs_total' => round($logsTotal, 2),
            'legacy_mercadopago_credits_total' => round($legacyMpTotal, 2),
            'payments_vs_logs_divergence' => round($divergence, 2),
            'healthy' => $stalePendingCredits === 0
                && $paidCreditsWithoutPayment === 0
                && $paymentsWithoutLog === 0
                && $divergence < 1.0,
        ];
    }
}
