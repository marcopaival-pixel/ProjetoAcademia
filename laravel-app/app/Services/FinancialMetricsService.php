<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\MercadoPagoCredit;
use App\Models\Payment;
use App\Models\Subscription;
use App\Support\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinancialMetricsService
{
    /** Status considerados como pagamento confirmado. */
    private const PAID_STATUSES = [
        Subscription::STATUS_FIN_ATIVO,
        'paid',
        'approved',
        'ATIVO',
    ];

    public function paidQuery()
    {
        return Payment::query()->whereIn('status', self::PAID_STATUSES);
    }

    public function totalRevenue(): float
    {
        return (float) $this->paidQuery()->sum('amount');
    }

    public function revenueSince(Carbon $startDate): float
    {
        return (float) $this->paidQuery()
            ->where('created_at', '>=', $startDate)
            ->sum('amount');
    }

    public function dailyRevenue(?Carbon $date = null): float
    {
        $date = $date ?? now();

        return (float) $this->paidQuery()
            ->whereDate('created_at', $date)
            ->sum('amount');
    }

    public function monthlyRevenue(?Carbon $month = null): float
    {
        $month = $month ?? now();

        return (float) $this->paidQuery()
            ->whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->sum('amount');
    }

    public function annualRevenue(?int $year = null): float
    {
        $year = $year ?? now()->year;

        return (float) $this->paidQuery()
            ->whereYear('created_at', $year)
            ->sum('amount');
    }

    public function paidPaymentsCount(): int
    {
        return $this->paidQuery()->count();
    }

    public function averageTicket(): float
    {
        $count = $this->paidPaymentsCount();

        return $count > 0 ? round($this->totalRevenue() / $count, 2) : 0.0;
    }

    public function revenueByGateway(): Collection
    {
        return $this->paidQuery()
            ->select('gateway', DB::raw('SUM(amount) as revenue'), DB::raw('COUNT(*) as total'))
            ->groupBy('gateway')
            ->orderByDesc('revenue')
            ->get();
    }

    public function revenueByClinic(int $limit = 10): Collection
    {
        return DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->join('academy_companies', 'academy_companies.id', '=', 'users.academy_company_id')
            ->whereIn('payments.status', self::PAID_STATUSES)
            ->select(
                'academy_companies.id',
                'academy_companies.name',
                'academy_companies.city',
                DB::raw('SUM(payments.amount) as revenue')
            )
            ->groupBy('academy_companies.id', 'academy_companies.name', 'academy_companies.city')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * LTV estimado: receita total / utilizadores pagantes únicos.
     */
    public function estimatedLtv(): float
    {
        $payingUsers = (int) $this->paidQuery()->distinct('user_id')->count('user_id');

        return $payingUsers > 0
            ? round($this->totalRevenue() / $payingUsers, 2)
            : 0.0;
    }

    /**
     * CAC estimado: comissões pagas no mês / novas assinaturas ativas no mês.
     */
    public function estimatedCac(?Carbon $month = null): float
    {
        $month = $month ?? now();

        $commissionsPaid = (float) Commission::query()
            ->where('status', Commission::STATUS_PAGO)
            ->whereMonth('paid_at', $month->month)
            ->whereYear('paid_at', $month->year)
            ->sum('paid_amount');

        $newSubscriptions = Subscription::query()
            ->whereCanonicalStatus(...SubscriptionStatus::mrrEligible())
            ->whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->count();

        if ($newSubscriptions === 0) {
            return 0.0;
        }

        return round($commissionsPaid / $newSubscriptions, 2);
    }

    public function revenueByPlan(int $limit = 10): Collection
    {
        return DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->join('subscriptions', 'subscriptions.user_id', '=', 'users.id')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->whereIn('payments.status', self::PAID_STATUSES)
            ->select(
                'plans.id',
                'plans.name',
                DB::raw('SUM(payments.amount) as revenue')
            )
            ->groupBy('plans.id', 'plans.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    public function aiCreditsRevenue(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = \App\Models\FinancialLog::query()
            ->where('action', 'AI_CREDITS_PURCHASED');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return (float) $query->sum('amount');
    }

    public function monthlyRevenueSeries(int $months = 12): array
    {
        $monthExpr = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $rows = $this->paidQuery()
            ->select(
                DB::raw("{$monthExpr} as month"),
                DB::raw('SUM(amount) as total')
            )
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy(DB::raw($monthExpr))
            ->orderBy('month')
            ->get();

        return $rows->map(fn ($r) => [
            'month' => $r->month,
            'label' => Carbon::createFromFormat('Y-m', $r->month)->translatedFormat('M/y'),
            'total' => round((float) $r->total, 2),
        ])->values()->all();
    }

    /**
     * Receita legada Mercado Pago sem duplicar registos já espelhados em `payments`.
     */
    public function legacyMercadoPagoRevenueExcludingPayments(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        ?string $planCode = null
    ): float {
        if (! Schema::hasTable('mercadopago_payment_credits')) {
            return 0.0;
        }

        $driver = DB::connection()->getDriverName();
        $gatewayMatchSql = $driver === 'sqlite'
            ? 'payments.gateway_id = CAST(mercadopago_payment_credits.mp_payment_id AS TEXT)'
            : 'payments.gateway_id = CAST(mercadopago_payment_credits.mp_payment_id AS CHAR)';

        $query = MercadoPagoCredit::query()
            ->whereNotExists(function ($sub) use ($gatewayMatchSql) {
                $sub->select(DB::raw(1))
                    ->from('payments')
                    ->where('gateway', 'mercadopago')
                    ->whereRaw($gatewayMatchSql);
            });

        if ($planCode !== null) {
            $query->where('plan_code', $planCode);
        }

        if ($startDate !== null) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate !== null) {
            $query->where('created_at', '<=', $endDate);
        }

        return (float) $query->sum('transaction_amount');
    }
}
