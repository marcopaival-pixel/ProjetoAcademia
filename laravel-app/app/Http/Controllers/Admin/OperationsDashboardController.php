<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Operations\WorkerManagementService;
use App\Services\Operations\SystemHealthService;
use App\Services\Operations\OperationalControlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class OperationsDashboardController extends Controller
{
    protected $healthService;
    protected $opsService;
    protected $workerService;

    public function __construct(
        SystemHealthService $healthService, 
        OperationalControlService $opsService,
        WorkerManagementService $workerService
    ) {
        $this->healthService = $healthService;
        $this->opsService = $opsService;
        $this->workerService = $workerService;
    }

    /**
     * Display the operational dashboard.
     */
    public function index()
    {
        $health = $this->healthService->checkAll();
        $settings = $this->opsService->getSettings();
        $workers = $this->workerService->getActiveWorkers();
        $logs = $this->getQuickLogs();
        
        return view('admin.operations.index', compact('health', 'settings', 'workers', 'logs'));
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

    /**
     * Restart all workers.
     */
    public function restartWorkers()
    {
        $result = $this->workerService->restartAll();
        return back()->with($result['status'], $result['message']);
    }

    /**
     * Clear queue.
     */
    public function clearQueue(Request $request)
    {
        $queue = $request->input('queue', 'default');
        $result = $this->workerService->clearQueue($queue);
        return back()->with($result['status'], $result['message']);
    }

    /**
     * Retry failed jobs.
     */
    public function retryFailed()
    {
        $result = $this->workerService->retryFailedJobs();
        return back()->with($result['status'], $result['message']);
    }

    /**
     * Flush failed jobs.
     */
    public function flushFailed()
    {
        $result = $this->workerService->flushFailedJobs();
        return back()->with($result['status'], $result['message']);
    }

    /**
     * Get recent logs for quick view.
     */
    protected function getQuickLogs(): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) return [];

        try {
            // Read last 50 lines
            $file = new \SplFileObject($logPath, 'r');
            $file->seek(PHP_INT_MAX);
            $lastLine = $file->key();
            
            $startLine = max(0, $lastLine - 50);
            $lines = [];
            
            $file->seek($startLine);
            while (!$file->eof()) {
                $line = trim($file->current());
                if (!empty($line)) {
                    // Try to extract timestamp and level
                    preg_match('/^\[(?P<date>.*)\] (?P<env>\w+)\.(?P<level>\w+): (?P<message>.*)/', $line, $matches);
                    if ($matches) {
                        $lines[] = [
                            'date' => $matches['date'],
                            'level' => strtolower($matches['level']),
                            'message' => $matches['message']
                        ];
                    } else {
                        $lines[] = ['message' => $line, 'level' => 'info', 'date' => ''];
                    }
                }
                $file->next();
            }
            
            return array_reverse($lines);
        } catch (\Exception $e) {
            return [['message' => 'Could not read logs: ' . $e->getMessage(), 'level' => 'error', 'date' => now()->toDateTimeString()]];
        }
    }
}

