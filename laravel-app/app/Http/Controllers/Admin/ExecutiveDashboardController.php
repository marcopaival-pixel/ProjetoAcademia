<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExecutiveDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ExecutiveDashboardController extends Controller
{
    public function __construct(
        protected ExecutiveDashboardService $executiveDashboard
    ) {}

    /**
     * Dashboard Executivo Inteligente — visão consolidada do ecossistema NexShape.
     */
    public function index(): View
    {
        $data = $this->executiveDashboard->getDashboardData();

        return view('admin.executive.dashboard', compact('data'));
    }

    /**
     * Endpoint JSON para refresh em tempo real (polling leve no frontend).
     */
    public function metrics(): JsonResponse
    {
        $this->executiveDashboard->clearCache();
        $data = $this->executiveDashboard->getDashboardData();

        return response()->json([
            'success' => true,
            'data' => $data,
            'refreshed_at' => now()->toIso8601String(),
        ]);
    }
}
