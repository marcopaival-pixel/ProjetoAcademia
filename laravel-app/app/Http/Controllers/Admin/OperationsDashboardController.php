<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Operations\SystemHealthService;
use App\Services\Operations\OperationalControlService;
use Illuminate\Http\Request;

class OperationsDashboardController extends Controller
{
    protected $healthService;
    protected $opsService;

    public function __construct(SystemHealthService $healthService, OperationalControlService $opsService)
    {
        $this->healthService = $healthService;
        $this->opsService = $opsService;
    }

    /**
     * Display the operational dashboard.
     */
    public function index()
    {
        $health = $this->healthService->checkAll();
        $settings = $this->opsService->getSettings();
        
        return view('admin.operations.index', compact('health', 'settings'));
    }

    /**
     * Update operational settings (Maintenance, Read-Only).
     */
    public function update(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'required|in:off,total,operable',
            'maintenance_message' => 'nullable|string',
            'read_only_mode' => 'required|boolean',
        ]);

        $this->opsService->updateSettings($request->only([
            'maintenance_mode',
            'maintenance_message',
            'read_only_mode'
        ]));

        return back()->with('success', 'Configurações operacionais atualizadas com sucesso!');
    }
}
