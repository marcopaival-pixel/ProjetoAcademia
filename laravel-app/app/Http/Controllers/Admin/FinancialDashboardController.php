<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AcademyCompany;
use App\Models\Subscription;
use App\Models\FinancialLog;
use App\Models\MercadoPagoCredit;
use App\Models\Payment;
use App\Services\FinancialMetricsService;
use App\Support\SubscriptionStatus;
use App\Models\AiCreditUsageLog;
use App\Models\AiCreditPurchaseLog;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialDashboardController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin.financial.dashboard');

        $period = $request->get('period', 'month'); // month, year, all
        $startDate = $this->getStartDate($period);
        $financial = app(FinancialMetricsService::class);

        $reconciliation = app(\App\Services\PaymentReconciliationService::class)->analyze(7, now()->subDays(30));

        $metrics = [
            'total_revenue' => $financial->totalRevenue(),
            'daily_revenue' => $financial->dailyRevenue(),
            'monthly_revenue' => $financial->monthlyRevenue(),
            'period_revenue' => $financial->revenueSince($startDate),
            'average_ticket' => $financial->averageTicket(),
            'estimated_ltv' => $financial->estimatedLtv(),
            'estimated_cac' => $financial->estimatedCac(),
            'revenue_by_plan' => $financial->revenueByPlan(),
            'revenue_by_gateway' => $financial->revenueByGateway(),
            'reconciliation' => $reconciliation,

            'paid_invoices' => $financial->paidPaymentsCount(),
            'legacy_mp_revenue' => $this->legacyRevenueExcludingPayments(),
            'pending_invoices' => Subscription::whereCanonicalStatus(SubscriptionStatus::PENDING)->count(),

            'active_users' => User::where('status', 'active')->count(),
            'delinquent_users' => Subscription::whereCanonicalStatus(...SubscriptionStatus::delinquent())->count(),
            'blocked_users' => User::where('status', 'blocked')->count(),

            'active_clinics' => AcademyCompany::where('is_active', true)->count(),
            'delinquent_clinics' => AcademyCompany::whereHas('subscriptions', function ($q) {
                $q->whereCanonicalStatus(...SubscriptionStatus::delinquent());
            })->count(),
            
            'ai_credits_sold' => AiCreditPurchaseLog::sum('credits_amount'),
            'ai_credits_used' => AiCreditUsageLog::sum('credits_consumed'),
            
            'revenue_by_clinic' => $financial->revenueByClinic(10),
        ];

        $metrics['revenue_by_type'] = [
            'payments_table' => $financial->totalRevenue(),
            'legacy_mercadopago_credits' => $this->legacyRevenueExcludingPayments(),
        ];

        return view('admin.financial.dashboard', compact('metrics', 'period'));
    }

    public function management(Request $request)
    {
        $this->authorize('admin.financial.management');

        $query = Subscription::with(['user', 'company', 'plan']);

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->whereHas('user', function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $subscriptions = $query->paginate(20);

        return view('admin.financial.management', compact('subscriptions'));
    }

    public function reports(Request $request)
    {
        $this->authorize('admin.financial.reports');

        $type = $request->get('type', 'revenue'); // revenue, delinquency, blocked, ai_credits, subscriptions
        
        // Lógica para cada relatório
        switch ($type) {
            case 'revenue':
                $data = $this->getRevenueReport($request);
                break;
            case 'delinquency':
                $data = $this->getDelinquencyReport($request);
                break;
            case 'ai_credits':
                $data = $this->getAiCreditsReport($request);
                break;
            case 'subscriptions':
                $data = $this->getSubscriptionsReport($request);
                break;
            case 'blocked':
                $data = $this->getBlockedReport($request);
                break;
            default:
                $data = [];
        }

        return view('admin.financial.reports', compact('data', 'type'));
    }

    public function processAction(Subscription $subscription, $action, Request $request)
    {
        $this->authorize('admin.financial.management');

        $service = app(\App\Services\SubscriptionService::class);
        $reason = $request->get('reason', 'Ação manual do administrador');

        switch ($action) {
            case 'suspend':
                $service->suspend($subscription, $reason);
                break;
            case 'block':
                $service->block($subscription, $reason);
                break;
            case 'release':
                $service->reactivate($subscription, 'Liberação manual');
                break;
            case 'reprocess':
                $service->processPaymentAttempt($subscription);
                break;
        }

        return back()->with('success', "Ação '{$action}' processada com sucesso.");
    }

    private function getStartDate($period)
    {
        return match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->subYears(10),
        };
    }

    private function getRevenueReport(Request $request)
    {
        return Payment::with(['user.academyCompany'])
            ->whereIn('status', [
                Subscription::STATUS_FIN_ATIVO,
                'paid',
                'approved',
            ])
            ->when($request->filled('start_date'), fn ($q) => $q->where('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->where('created_at', '<=', $request->end_date))
            ->when($request->filled('company_id'), function ($q) use ($request) {
                $q->whereHas('user', fn ($qu) => $qu->where('academy_company_id', $request->company_id));
            })
            ->latest()
            ->get();
    }

    private function getDelinquencyReport(Request $request)
    {
        return Subscription::with(['user', 'company', 'plan'])
            ->whereCanonicalStatus(...SubscriptionStatus::delinquent())
            ->when($request->filled('company_id'), fn($q) => $q->where('academy_company_id', $request->company_id))
            ->latest()
            ->get();
    }

    private function getAiCreditsReport(Request $request)
    {
        return AiCreditUsageLog::with('user.academyCompany')
            ->when($request->filled('start_date'), fn($q) => $q->where('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->where('created_at', '<=', $request->end_date))
            ->when($request->filled('company_id'), function($q) use ($request) {
                $q->whereHas('user', fn($qu) => $qu->where('academy_company_id', $request->company_id));
            })
            ->latest()
            ->get();
    }

    private function getSubscriptionsReport(Request $request)
    {
        return Subscription::with(['user', 'company', 'plan'])
            ->when($request->filled('start_date'), fn($q) => $q->where('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->where('created_at', '<=', $request->end_date))
            ->when($request->filled('company_id'), fn($q) => $q->where('academy_company_id', $request->company_id))
            ->latest()
            ->get();
    }

    private function getBlockedReport(Request $request)
    {
        return Subscription::with(['user', 'company', 'plan'])
            ->where('status', Subscription::STATUS_BLOCKED)
            ->when($request->filled('company_id'), fn($q) => $q->where('academy_company_id', $request->company_id))
            ->latest()
            ->get();
    }

    /**
     * Receita legada MP excluindo valores já importados na tabela payments.
     */
    private function legacyRevenueExcludingPayments(): float
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('mercadopago_payment_credits')) {
            return 0.0;
        }

        return (float) MercadoPagoCredit::query()
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('payments')
                    ->where('gateway', 'mercadopago')
                    ->whereRaw('payments.gateway_id = CAST(mercadopago_payment_credits.mp_payment_id AS CHAR)');
            })
            ->sum('transaction_amount');
    }
}
