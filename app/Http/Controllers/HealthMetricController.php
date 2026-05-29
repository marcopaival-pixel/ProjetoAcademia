<?php

namespace App\Http\Controllers;

use App\Models\HealthMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthMetricController extends Controller
{
    /**
     * Lista as métricas recentes do usuário.
     */
    /**
     * Lista as métricas recentes do usuário e exibe o dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Dados para o dashboard (últimos 30 dias para os gráficos)
        $history = HealthMetric::where('user_id', $user->id)
            ->where('recorded_at', '>=', now()->subDays(30))
            ->orderBy('recorded_at', 'asc')
            ->get()
            ->groupBy('type');

        // Métricas atuais (última de cada tipo)
        $latest = [
            'hrv' => $user->healthMetrics()->where('type', 'hrv')->latest('recorded_at')->first(),
            'sleep' => $user->healthMetrics()->where('type', 'sleep_hours')->latest('recorded_at')->first(),
            'recovery' => $user->healthMetrics()->where('type', 'recovery_score')->latest('recorded_at')->first(),
            'resting_hr' => $user->healthMetrics()->where('type', 'resting_hr')->latest('recorded_at')->first(),
        ];

        if (request()->wantsJson()) {
            return response()->json([
                'history' => $history,
                'latest' => $latest
            ]);
        }

        return view('health-metrics.index', compact('history', 'latest'));
    }

    /**
     * Armazena uma nova métrica (Ingestão via API ou Manual).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:hrv,sleep_hours,sleep_quality,recovery_score,resting_hr,spo2',
            'value' => 'required|numeric',
            'unit' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:50',
            'recorded_at' => 'required|date',
            'metadata' => 'nullable|array'
        ]);

        $metric = Auth::user()->healthMetrics()->create($validated);

        return response()->json([
            'message' => 'Métrica registrada com sucesso!',
            'data' => $metric
        ], 201);
    }

    /**
     * Dashboard de métricas (Resumo para o frontend).
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $summary = [
            'last_hrv' => $user->healthMetrics()->ofType(HealthMetric::TYPE_HRV)->recent()->first(),
            'last_sleep' => $user->healthMetrics()->ofType(HealthMetric::TYPE_SLEEP_HOURS)->recent()->first(),
            'last_recovery' => $user->healthMetrics()->ofType(HealthMetric::TYPE_RECOVERY)->recent()->first(),
            'averages_last_7_days' => $this->calculateAverages($user, 7)
        ];

        return response()->json($summary);
    }

    private function calculateAverages($user, int $days)
    {
        return HealthMetric::where('user_id', $user->id)
            ->where('recorded_at', '>=', now()->subDays($days))
            ->selectRaw('type, AVG(value) as average')
            ->groupBy('type')
            ->get();
    }
}
