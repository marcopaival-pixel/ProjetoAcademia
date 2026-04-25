<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AcademyCompany;
use App\Models\Subscription;
use App\Models\FinancialLog;
use App\Models\MercadoPagoCredit;
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
        $period = $request->get('period', 'month'); // month, year, all
        $startDate = $this->getStartDate($period);

        $metrics = [
            'total_revenue' => MercadoPagoCredit::sum('transaction_amount'),
            'monthly_revenue' => MercadoPagoCredit::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('transaction_amount'),
            'period_revenue' => MercadoPagoCredit::where('created_at', '>=', $startDate)->sum('transaction_amount'),
            
            'paid_invoices' => MercadoPagoCredit::count(),
            'pending_invoices' => Subscription::whereIn('status', [Subscription::FIN_PENDENTE, Subscription::STATUS_PENDING])->count(),
            
            'active_users' => User::where('status', 'active')->count(),
            'delinquent_users' => Subscription::whereIn('status', [Subscription::FIN_ATRASADO, Subscription::STATUS_OVERDUE])->count(),
            'blocked_users' => User::where('status', 'blocked')->count(),
            
            'active_clinics' => AcademyCompany::where('is_active', true)->count(),
            'delinquent_clinics' => AcademyCompany::whereHas('subscriptions', function($q) {
                $q->whereIn('status', [Subscription::FIN_ATRASADO, Subscription::STATUS_OVERDUE]);
            })->count(),
            
            'ai_credits_sold' => AiCreditPurchaseLog::sum('credits_amount'),
            'ai_credits_used' => AiCreditUsageLog::sum('credits_consumed'),
            
            'revenue_by_plan' => MercadoPagoCredit::select('plan_code as name', DB::raw('sum(transaction_amount) as revenue'))
                ->groupBy('plan_code')
                ->get(),
            'revenue_by_clinic' => AcademyCompany::join('users', 'users.academy_company_id', '=', 'academy_companies.id')
                ->join('mercadopago_payment_credits', 'mercadopago_payment_credits.user_id', '=', 'users.id')
                ->select('academy_companies.name', 'academy_companies.city', DB::raw('sum(mercadopago_payment_credits.transaction_amount) as revenue'))
                ->groupBy('academy_companies.id', 'academy_companies.name', 'academy_companies.city')
                ->orderBy('revenue', 'desc')
                ->limit(10)
                ->get(),
        ];

        // Receita por tipo
        $metrics['revenue_by_type'] = [
            'subscription' => MercadoPagoCredit::where('plan_code', '!=', 'ai_credits')->sum('transaction_amount'),
            'ai_credits' => MercadoPagoCredit::where('plan_code', 'ai_credits')->sum('transaction_amount'),
        ];

        return view('admin.financial.dashboard', compact('metrics', 'period'));
    }

    public function management(Request $request)
    {
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
            default:
                $data = [];
        }

        return view('admin.financial.reports', compact('data', 'type'));
    }

    public function processAction(Subscription $subscription, $action, Request $request)
    {
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
        return MercadoPagoCredit::with('user')
            ->when($request->filled('start_date'), fn($q) => $q->where('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->where('created_at', '<=', $request->end_date))
            ->get();
    }

    private function getDelinquencyReport(Request $request)
    {
        return Subscription::with(['user', 'company', 'plan'])
            ->whereIn('status', [Subscription::FIN_ATRASADO, Subscription::FIN_SUSPENSO, Subscription::FIN_BLOQUEADO, Subscription::STATUS_OVERDUE])
            ->get();
    }

    private function getAiCreditsReport(Request $request)
    {
        return AiCreditUsageLog::with('user')->get();
    }
}
