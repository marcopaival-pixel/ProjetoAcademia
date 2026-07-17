<?php

namespace App\Services;

use App\Models\MercadoPagoSubscription;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SaaSMetricsService
{
    /**
     * Calcula o MRR (Monthly Recurring Revenue) com cache de 6 horas.
     * Retorna o valor base do faturamento recorrente.
     */
    public function calculateMRR(): float
    {
        return Cache::remember('saas_metrics_mrr', 360, function () {
            // Em um cenário real de alta escala, usaríamos queries SQL agregadas.
            // Para manter compatibilidade com o atual:
            $activeSubs = MercadoPagoSubscription::where('status', 'authorized')->get();
            $mrr = 0;

            foreach ($activeSubs as $sub) {
                if ($sub->plan_code === 'monthly') {
                    $mrr += 19.9; // Ideal: buscar do Plan Model
                } elseif ($sub->plan_code === 'yearly') {
                    $mrr += (149.9 / 12);
                }
            }

            return round($mrr, 2);
        });
    }

    /**
     * Retorna a quantidade de assinaturas ativas com cache de 6 horas.
     */
    public function getActiveSubscriptionsCount(): int
    {
        return Cache::remember('saas_metrics_active_subs', 360, function () {
            return MercadoPagoSubscription::where('status', 'authorized')->count();
        });
    }

    /**
     * Limpa o cache das métricas, ideal para chamar em webhooks de pagamento aprovado.
     */
    public function clearCache(): void
    {
        Cache::forget('saas_metrics_mrr');
        Cache::forget('saas_metrics_active_subs');
        Cache::forget('saas_metrics_churn_rate');
    }

    /**
     * Exemplo de cálculo de Churn Rate Mensal.
     * (Assinaturas Canceladas no Mês Atual) / (Assinaturas Ativas no Início do Mês)
     */
    public function getChurnRate(): float
    {
        return Cache::remember('saas_metrics_churn_rate', 360, function () {
            $cancelledThisMonth = MercadoPagoSubscription::whereIn('status', ['cancelled', 'canceled'])
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count();

            // Total histórico (ativas + canceladas neste mês) - Simplificação
            $totalBase = MercadoPagoSubscription::whereIn('status', ['authorized', 'cancelled', 'canceled'])->count();

            if ($totalBase === 0) {
                return 0.0;
            }

            return round(($cancelledThisMonth / $totalBase) * 100, 2);
        });
    }
}
