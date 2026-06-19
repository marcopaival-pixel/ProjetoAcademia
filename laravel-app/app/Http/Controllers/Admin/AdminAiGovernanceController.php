<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIOrchestratorLog;
use App\Models\AiCreditTransaction;
use App\Models\AiCreditUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAiGovernanceController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $startDate = now()->subDays($days)->startOfDay();
        $today = today();

        $orchestratorQuery = AIOrchestratorLog::where('created_at', '>=', $startDate);

        $metrics = [
            'total_cost_usd' => (float) $orchestratorQuery->clone()->sum('cost_usd'),
            'total_tokens' => (int) $orchestratorQuery->clone()->sum('total_tokens'),
            'total_requests' => (int) $orchestratorQuery->clone()->count(),
            'cost_today_usd' => (float) AIOrchestratorLog::whereDate('created_at', $today)->sum('cost_usd'),
            'tokens_today' => (int) AIOrchestratorLog::whereDate('created_at', $today)->sum('total_tokens'),
            'requests_today' => (int) AIOrchestratorLog::whereDate('created_at', $today)->count(),
            'error_rate' => $this->errorRate($startDate),
            'credits_consumed' => (int) AiCreditUsageLog::where('created_at', '>=', $startDate)->sum('credits_consumed'),
            'credits_today' => (int) AiCreditUsageLog::whereDate('created_at', $today)->sum('credits_consumed'),
        ];

        $byAgent = AIOrchestratorLog::select(
            'agent_name',
            DB::raw('count(*) as count'),
            DB::raw('sum(cost_usd) as total_cost'),
            DB::raw('sum(total_tokens) as total_tokens')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('agent_name')
            ->orderByDesc('total_cost')
            ->get();

        $byClinic = AIOrchestratorLog::select(
            'clinic_id',
            DB::raw('count(*) as count'),
            DB::raw('sum(cost_usd) as total_cost'),
            DB::raw('sum(total_tokens) as total_tokens')
        )
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('clinic_id')
            ->groupBy('clinic_id')
            ->orderByDesc('total_cost')
            ->limit(15)
            ->get();

        $byFeature = AiCreditUsageLog::select(
            'action_type',
            DB::raw('count(*) as count'),
            DB::raw('sum(credits_consumed) as total_credits')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('action_type')
            ->orderByDesc('total_credits')
            ->get();

        $topUsers = AIOrchestratorLog::select(
            'user_id',
            DB::raw('sum(cost_usd) as total_cost'),
            DB::raw('sum(total_tokens) as total_tokens'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->orderByDesc('total_cost')
            ->limit(10)
            ->with('user:id,name,email')
            ->get();

        $dailyTrend = AIOrchestratorLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('sum(cost_usd) as cost'),
            DB::raw('sum(total_tokens) as tokens')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return view('admin.ai.governance-dashboard', compact(
            'metrics',
            'byAgent',
            'byClinic',
            'byFeature',
            'topUsers',
            'dailyTrend',
            'days'
        ));
    }

    private function errorRate(Carbon $startDate): float
    {
        $total = AIOrchestratorLog::where('created_at', '>=', $startDate)->count();
        if ($total === 0) {
            return 0.0;
        }

        $errors = AIOrchestratorLog::where('created_at', '>=', $startDate)
            ->whereIn('status', ['error', 'limit_reached'])
            ->count();

        return round(($errors / $total) * 100, 2);
    }
}
