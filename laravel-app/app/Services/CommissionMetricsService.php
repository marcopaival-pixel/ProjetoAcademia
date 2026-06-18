<?php

namespace App\Services;

use App\Models\Commission;
use Illuminate\Support\Facades\Cache;

class CommissionMetricsService
{
    public function summary(): array
    {
        return Cache::remember('commission_metrics_summary', 300, function () {
            $pendingStatuses = [
                Commission::STATUS_PENDENTE,
                Commission::STATUS_AGUARDANDO_PAGAMENTO,
                Commission::STATUS_CARENCIA,
            ];

            $pendingTotal = (float) Commission::query()
                ->whereIn('status', $pendingStatuses)
                ->sum('commission_amount');

            $availableTotal = (float) Commission::query()
                ->where('status', Commission::STATUS_DISPONIVEL)
                ->sum('commission_amount');

            $paidTotal = (float) Commission::query()
                ->where('status', Commission::STATUS_PAGO)
                ->sum('commission_amount');

            $paidMonth = (float) Commission::query()
                ->where('status', Commission::STATUS_PAGO)
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('commission_amount');

            $clawbackMonth = (float) Commission::query()
                ->where('status', Commission::STATUS_CLAWBACK)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('commission_amount');

            $cancelledMonth = Commission::query()
                ->where('status', Commission::STATUS_CANCELADO)
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count();

            $generatedMonth = (float) Commission::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereNotIn('status', [Commission::STATUS_CANCELADO, Commission::STATUS_CLAWBACK])
                ->sum('commission_amount');

            return [
                'pending_count' => Commission::whereIn('status', $pendingStatuses)->count(),
                'pending_total' => round($pendingTotal, 2),
                'available_count' => Commission::where('status', Commission::STATUS_DISPONIVEL)->count(),
                'available_total' => round($availableTotal, 2),
                'paid_total' => round($paidTotal, 2),
                'paid_month' => round($paidMonth, 2),
                'clawback_month' => round(abs($clawbackMonth), 2),
                'cancelled_month' => $cancelledMonth,
                'generated_month' => round($generatedMonth, 2),
            ];
        });
    }

    public function clearCache(): void
    {
        Cache::forget('commission_metrics_summary');
    }
}
