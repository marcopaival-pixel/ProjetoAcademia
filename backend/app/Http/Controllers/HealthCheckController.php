<?php

namespace App\Http\Controllers;

use App\Services\Operations\SystemHealthService;
use Illuminate\Http\JsonResponse;

class HealthCheckController extends Controller
{
    protected $healthService;

    public function __construct(SystemHealthService $healthService)
    {
        $this->healthService = $healthService;
    }

    /**
     * Return system health status in JSON.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $health = $this->healthService->checkAll();
        
        $status = in_array($health['status'], ['critical', 'unhealthy'], true) ? 503 : 200;

        return response()->json($health, $status);
    }
}
