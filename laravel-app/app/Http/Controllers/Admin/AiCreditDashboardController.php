<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\AiCreditUsageLog;
use App\Models\AiCreditPurchaseLog;
use App\Models\FinancialLog;
use App\Services\FinancialMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiCreditDashboardController extends Controller
{
    /**
     * Display the AI Credits Dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // Default 30 days
        $userType = $request->get('user_type');
        $status = $request->get('status');
        
        $startDate = $this->getStartDate($period, $request->get('start_date'));
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now();

        // Base query for users to calculate metrics
        $userQuery = User::query();
        if ($userType) {
            $userQuery->whereHas('roles', function($q) use ($userType) {
                $q->where('name', $userType);
            });
        }
        if ($status) {
            $userQuery->where('status', $status);
        }

        $financial = app(FinancialMetricsService::class);

        $metrics = [
            'total_sold' => AiCreditPurchaseLog::whereBetween('created_at', [$startDate, $endDate])->sum('credits_amount'),
            'total_consumed' => AiCreditUsageLog::whereBetween('created_at', [$startDate, $endDate])->sum('credits_consumed'),
            'available' => User::sum('ai_credits'),
            'revenue' => $financial->aiCreditsRevenue($startDate, $endDate),
            'legacy_mp_revenue' => $financial->legacyMercadoPagoRevenueExcludingPayments($startDate, $endDate, 'ai_credits'),
            'active_users_ia' => User::where('ai_credits', '>', 0)->count(),
        ];

        $metrics['avg_consumption'] = $metrics['active_users_ia'] > 0 
            ? $metrics['total_consumed'] / $metrics['active_users_ia'] 
            : 0;

        // Audit Logs (Operações de Créditos)
        $auditLogs = \App\Models\FinancialLog::whereIn('action', ['AI_CREDITS_ADDED', 'AI_CREDITS_CONSUMED', 'AI_CREDITS_PURCHASED', 'AI_CREDITS_DISTRIBUTED'])
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Charts Data
        $charts = [
            'consumption_by_day' => $this->getConsumptionByDay($startDate, $endDate),
            'revenue_by_day' => $this->getRevenueByDay($startDate, $endDate),
            'consumption_by_type' => $this->getConsumptionByType($startDate, $endDate),
        ];

        $roles = Role::all();

        return view('admin.financial.ai-credits-dashboard', compact('metrics', 'charts', 'period', 'userType', 'status', 'roles', 'startDate', 'endDate', 'auditLogs'));
    }

    /**
     * Get detailed consumption report per user.
     */
    public function report(Request $request)
    {
        $query = User::with(['roles', 'plan'])
            ->select('users.*')
            ->withSum(['aiUsage as total_used'], 'credits_consumed')
            ->withSum(['aiUsage as used_in_period' => function($q) use ($request) {
                $startDate = $this->getStartDate($request->get('period', '30'), $request->get('start_date'));
                $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now();
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'credits_consumed');

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('user_type')) {
            $userType = $request->get('user_type');
            $query->whereHas('roles', function($q) use ($userType) {
                $q->where('name', $userType);
            });
        }

        $users = $query->orderBy('ai_credits', 'desc')->paginate(20);

        return view('admin.financial.ai-credits-report', compact('users'));
    }

    private function getStartDate($period, $customDate = null)
    {
        if ($customDate) return Carbon::parse($customDate)->startOfDay();

        return match ($period) {
            'today' => now()->startOfDay(),
            '7' => now()->subDays(7)->startOfDay(),
            '30' => now()->subDays(30)->startOfDay(),
            'all' => now()->subYears(10),
            default => now()->subDays(30)->startOfDay(),
        };
    }

    private function getConsumptionByDay($startDate, $endDate)
    {
        $format = DB::connection()->getDriverName() === 'sqlite' ? "%Y-%m-%d" : "%Y-%m-%d";
        $dateFunc = DB::connection()->getDriverName() === 'sqlite' 
            ? "strftime('{$format}', created_at)" 
            : "DATE_FORMAT(created_at, '{$format}')";

        return AiCreditUsageLog::select(DB::raw("{$dateFunc} as date"), DB::raw('sum(credits_consumed) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRevenueByDay($startDate, $endDate)
    {
        $format = DB::connection()->getDriverName() === 'sqlite' ? "%Y-%m-%d" : "%Y-%m-%d";
        $dateFunc = DB::connection()->getDriverName() === 'sqlite' 
            ? "strftime('{$format}', created_at)" 
            : "DATE_FORMAT(created_at, '{$format}')";

        return FinancialLog::select(DB::raw("{$dateFunc} as date"), DB::raw('sum(amount) as total'))
            ->where('action', 'AI_CREDITS_PURCHASED')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getConsumptionByType($startDate, $endDate)
    {
        return DB::table('ai_credits_usage_logs')
            ->join('user_roles', 'ai_credits_usage_logs.user_id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->select('roles.name as type', DB::raw('sum(credits_consumed) as total'))
            ->whereBetween('ai_credits_usage_logs.created_at', [$startDate, $endDate])
            ->groupBy('roles.name')
            ->get();
    }
}
