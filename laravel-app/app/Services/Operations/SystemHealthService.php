<?php

namespace App\Services\Operations;

use App\Jobs\QueueHeartbeatJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemHealthService
{
    /**
     * Check all system components.
     *
     * @return array
     */
    public function checkAll(): array
    {
        $start = microtime(true);

        $health = [
            'database' => $this->checkDatabase(),
            'queue' => $this->checkQueue(),
            'cache' => $this->checkCache(),
            'disk' => $this->checkDisk(),
            'cpu' => $this->checkCpu(),
            'memory' => $this->checkMemory(),
            'timestamp' => now()->toIso8601String(),
        ];

        $health['response_time'] = round((microtime(true) - $start) * 1000, 2) . 'ms';
        $health['status'] = $this->determineOverallStatus($health);

        return $health;
    }

    /**
     * Check database connection.
     */
    public function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Connected'];
        } catch (\Exception $e) {
            Log::critical('Database health check failed: ' . $e->getMessage());
            return ['status' => 'fail', 'message' => 'Connection failed'];
        }
    }

    /**
     * Check if queue workers are active.
     */
    public function checkQueue(): array
    {
        try {
            $connection = (string) config('queue.default');
            if ($connection === 'sync') {
                return [
                    'status' => 'ok',
                    'connection' => $connection,
                    'message' => 'Synchronous queue does not require a worker',
                ];
            }

            $failedJobs = DB::table('failed_jobs')->where('failed_at', '>', now()->subMinutes(5))->count();

            $lastHeartbeat = Cache::get('operations.queue_heartbeat_at');
            QueueHeartbeatJob::dispatch();

            if ($lastHeartbeat === null) {
                return [
                    'status' => 'warning',
                    'failed_recent' => $failedJobs,
                    'connection' => $connection,
                    'message' => 'Queue heartbeat pending',
                ];
            }

            $lastHeartbeatAt = \Carbon\Carbon::parse($lastHeartbeat);
            if ($lastHeartbeatAt->lt(now()->subMinutes(3))) {
                return [
                    'status' => 'fail',
                    'failed_recent' => $failedJobs,
                    'connection' => $connection,
                    'last_heartbeat_at' => $lastHeartbeatAt->toIso8601String(),
                    'message' => 'Queue worker heartbeat is stale',
                ];
            }

            return [
                'status' => 'ok',
                'failed_recent' => $failedJobs,
                'connection' => $connection,
                'last_heartbeat_at' => $lastHeartbeatAt->toIso8601String(),
            ];
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check cache connection.
     */
    public function checkCache(): array
    {
        try {
            Cache::put('health_check', true, 10);
            $val = Cache::get('health_check');
            return ['status' => $val ? 'ok' : 'fail'];
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check disk space.
     */
    public function checkDisk(): array
    {
        $free = disk_free_space(base_path());
        $total = disk_total_space(base_path());
        $usedPercent = round((($total - $free) / $total) * 100, 2);

        return [
            'status' => $usedPercent > 90 ? 'warning' : 'ok',
            'free' => $this->formatBytes($free),
            'used_percent' => $usedPercent,
        ];
    }

    /**
     * Check CPU load.
     */
    public function checkCpu(): array
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                'status' => $load[0] > 2.0 ? 'warning' : 'ok', // Arbitrary threshold
                'load_1m' => $load[0],
                'load_5m' => $load[1],
                'load_15m' => $load[2],
            ];
        }

        return ['status' => 'unknown', 'message' => 'sys_getloadavg not available'];
    }

    /**
     * Check memory usage.
     */
    public function checkMemory(): array
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return ['status' => 'unknown', 'message' => 'Memory check not implemented for Windows'];
        }

        try {
            $free = shell_exec('free');
            $free = (string)trim($free);
            $free_arr = explode("\n", $free);
            $mem = explode(" ", $free_arr[1]);
            $mem = array_filter($mem);
            $mem = array_values($mem);
            $usedPercent = round($mem[2] / $mem[1] * 100, 2);

            return [
                'status' => $usedPercent > 90 ? 'warning' : 'ok',
                'used_percent' => $usedPercent,
                'total' => $this->formatBytes($mem[1] * 1024),
            ];
        } catch (\Exception $e) {
            return ['status' => 'unknown', 'message' => $e->getMessage()];
        }
    }

    /**
     * Determine overall system status.
     */
    private function determineOverallStatus(array $health): string
    {
        if ($health['database']['status'] === 'fail') return 'critical';
        
        foreach ($health as $component) {
            if (is_array($component) && isset($component['status'])) {
                if ($component['status'] === 'fail') return 'unhealthy';
                if ($component['status'] === 'warning') return 'degraded';
            }
        }

        return 'healthy';
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
