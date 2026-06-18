<?php

namespace App\Services;

use App\Models\MercadoPagoSubscription;
use App\Models\Plan;
use App\Models\Subscription;
use App\Support\SubscriptionStatus;
use Illuminate\Support\Facades\Cache;

class SaaSMetricsService
{
    /**
     * Calcula o MRR (Monthly Recurring Revenue) com cache de 6 horas.
     */
    public function calculateMRR(): float
    {
        return Cache::remember('saas_metrics_mrr', 360, function () {
            $mrr = 0.0;

            $subscriptions = Subscription::with('plan')
                ->whereCanonicalStatus(...SubscriptionStatus::mrrEligible())
                ->get();

            foreach ($subscriptions as $subscription) {
                $mrr += $this->monthlyValueForPlan($subscription->plan, '');
            }

            $mpUserIds = $subscriptions->pluck('user_id')->filter()->unique();

            $mpSubs = MercadoPagoSubscription::where('status', 'authorized')
                ->when($mpUserIds->isNotEmpty(), fn ($q) => $q->whereNotIn('user_id', $mpUserIds))
                ->get();

            foreach ($mpSubs as $mpSub) {
                $plan = $this->resolvePlanByCode((string) $mpSub->plan_code);
                $mrr += $this->monthlyValueForPlan($plan, (string) $mpSub->plan_code);
            }

            return round($mrr, 2);
        });
    }

    public function getActiveSubscriptionsCount(): int
    {
        return Cache::remember('saas_metrics_active_subs', 360, function () {
            $platformUserIds = Subscription::whereCanonicalStatus(...SubscriptionStatus::mrrEligible())
                ->pluck('user_id')
                ->filter()
                ->unique();

            $mpOnlyCount = MercadoPagoSubscription::where('status', 'authorized')
                ->when($platformUserIds->isNotEmpty(), fn ($q) => $q->whereNotIn('user_id', $platformUserIds))
                ->distinct('user_id')
                ->count('user_id');

            return $platformUserIds->count() + $mpOnlyCount;
        });
    }

    public function clearCache(): void
    {
        Cache::forget('saas_metrics_mrr');
        Cache::forget('saas_metrics_active_subs');
        Cache::forget('saas_metrics_churn_rate');
    }

    public function getChurnRate(): float
    {
        return Cache::remember('saas_metrics_churn_rate', 360, function () {
            $cancelledThisMonth = Subscription::whereCanonicalStatus(SubscriptionStatus::CANCELLED)
                ->whereMonth('cancelled_at', now()->month)
                ->whereYear('cancelled_at', now()->year)
                ->count();

            $mpCancelled = MercadoPagoSubscription::whereIn('status', ['cancelled', 'canceled'])
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count();

            $cancelled = $cancelledThisMonth + $mpCancelled;

            $activeBase = Subscription::whereCanonicalStatus(...SubscriptionStatus::mrrEligible())->count()
                + MercadoPagoSubscription::where('status', 'authorized')->count();

            $totalBase = max(1, $activeBase + $cancelled);

            return round(($cancelled / $totalBase) * 100, 2);
        });
    }

    private function resolvePlanByCode(string $planCode): ?Plan
    {
        if ($planCode === '') {
            return null;
        }

        return Plan::query()
            ->where('name', $planCode)
            ->orWhere('name', 'like', '%'.$planCode.'%')
            ->orderBy('id')
            ->first();
    }

    private function monthlyValueForPlan(?Plan $plan, string $fallbackCode): float
    {
        if ($plan) {
            $price = (float) $plan->price;

            if ($this->isAnnualPlan($plan, $fallbackCode)) {
                return $price / 12;
            }

            return $price;
        }

        return match ($fallbackCode) {
            'yearly', 'anual', 'annual' => 149.9 / 12,
            'monthly', 'mensal' => 19.9,
            default => 0.0,
        };
    }

    private function isAnnualPlan(Plan $plan, string $fallbackCode): bool
    {
        $needles = ['yearly', 'anual', 'annual', 'ano'];

        $haystacks = [
            strtolower((string) $plan->type),
            strtolower((string) $plan->name),
            strtolower($fallbackCode),
        ];

        foreach ($haystacks as $value) {
            foreach ($needles as $needle) {
                if ($value !== '' && str_contains($value, $needle)) {
                    return true;
                }
            }
        }

        return false;
    }
}
