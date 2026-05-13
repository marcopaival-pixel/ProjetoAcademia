<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIOrchestratorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminOrchestratorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $startDate = now()->subDays($days)->startOfDay();

        // 1. Métricas Gerais
        $metrics = [
            'total_cost' => AIOrchestratorLog::where('created_at', '>=', $startDate)->sum('cost_usd'),
            'total_tokens' => AIOrchestratorLog::where('created_at', '>=', $startDate)->sum('total_tokens'),
            'total_requests' => AIOrchestratorLog::where('created_at', '>=', $startDate)->count(),
            'avg_response_time' => AIOrchestratorLog::where('created_at', '>=', $startDate)->avg('execution_time_ms'),
            'error_rate' => $this->calculateErrorRate($startDate),
        ];

        // 2. Distribuição por Agente
        $byAgent = AIOrchestratorLog::select('agent_name', 
                DB::raw('count(*) as count'), 
                DB::raw('sum(cost_usd) as total_cost'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('agent_name')
            ->orderByDesc('total_cost')
            ->get();

        // 3. Distribuição por Modelo
        $byModel = AIOrchestratorLog::select('model_name', 
                DB::raw('count(*) as count'), 
                DB::raw('sum(cost_usd) as total_cost'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('model_name')
            ->orderByDesc('total_cost')
            ->get();

        // 4. Últimos Logs
        $recentLogs = AIOrchestratorLog::with('user')
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.ai.orchestrator-dashboard', compact('metrics', 'byAgent', 'byModel', 'recentLogs', 'days'));
    }

    private function calculateErrorRate($startDate)
    {
        $total = AIOrchestratorLog::where('created_at', '>=', $startDate)->count();
        if ($total === 0) return 0;

        $errors = AIOrchestratorLog::where('created_at', '>=', $startDate)
            ->where('status', 'error')
            ->count();

        return ($errors / $total) * 100;
    }
}
