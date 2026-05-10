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
    public function index()
    {
        $metrics = Auth::user()->healthMetrics()
            ->recent()
            ->paginate(20);

        return response()->json($metrics);
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
