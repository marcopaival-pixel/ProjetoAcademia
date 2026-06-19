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
            'jobs' => $this->getJobStats(),
            'queue_breakdown' => $this->getQueueBreakdown(),
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

            $failedJobs = DB::table('failed_jobs')->where('failed_at', '>', now()->subMinutes(60))->count();
            $pendingJobs = DB::table('jobs')->count();

            $lastHeartbeat = Cache::get('operations.queue_heartbeat_at');
            
            // Dispatch heartbeat if not recently dispatched
            if (!Cache::has('operations.heartbeat_dispatched')) {
                try {
                    \App\Jobs\QueueHeartbeatJob::dispatch();
                    Cache::put('operations.heartbeat_dispatched', true, 30);
                } catch (\Exception $e) {
                    Log::warning('Could not dispatch queue heartbeat: ' . $e->getMessage());
                }
            }

            if ($lastHeartbeat === null) {
                return [
                    'status' => 'warning',
                    'pending' => $pendingJobs,
                    'failed_recent' => $failedJobs,
                    'connection' => $connection,
                    'message' => 'Queue heartbeat pending',
                ];
            }

            $lastHeartbeatAt = \Carbon\Carbon::parse($lastHeartbeat);
            if ($lastHeartbeatAt->lt(now()->subMinutes(5))) {
                return [
                    'status' => 'fail',
                    'pending' => $pendingJobs,
                    'failed_recent' => $failedJobs,
                    'connection' => $connection,
                    'last_heartbeat_at' => $lastHeartbeatAt->toIso8601String(),
                    'message' => 'Queue worker heartbeat is stale',
                ];
            }

            return [
                'status' => 'ok',
                'pending' => $pendingJobs,
                'failed_recent' => $failedJobs,
                'connection' => $connection,
                'last_heartbeat_at' => $lastHeartbeatAt->toIso8601String(),
            ];
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get statistics about jobs.
     */
    public function getJobStats(): array
    {
        try {
            $totalFailed = DB::table('failed_jobs')->count();
            $totalPending = DB::table('jobs')->count();
            $failedRecent = DB::table('failed_jobs')
                ->where('failed_at', '>', now()->subHour())
                ->count();

            $completedLastHour = (int) Cache::get('operations.jobs_completed_last_hour', 0);
            $totalDurationMs = (int) Cache::get('operations.jobs_duration_ms_last_hour', 0);

            $throughput = $completedLastHour;
            $avgTime = 'N/A';
            if ($completedLastHour > 0) {
                $avgMs = (int) round($totalDurationMs / $completedLastHour);
                $avgTime = $avgMs >= 1000
                    ? round($avgMs / 1000, 1).'s'
                    : $avgMs.'ms';
            }

            return [
                'pending' => $totalPending,
                'failed' => $totalFailed,
                'failed_last_hour' => $failedRecent,
                'completed_last_hour' => $completedLastHour,
                'avg_time' => $avgTime,
                'throughput' => $throughput.' j/h',
            ];
        } catch (\Exception $e) {
            return ['pending' => 0, 'failed' => 0, 'avg_time' => 'N/A', 'throughput' => '0 j/h'];
        }
    }

    /**
     * Pending jobs grouped by queue name.
     *
     * @return array<string, int>
     */
    public function getQueueBreakdown(): array
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('jobs')) {
                return [];
            }

            $rows = DB::table('jobs')
                ->select('queue', DB::raw('COUNT(*) as total'))
                ->groupBy('queue')
                ->pluck('total', 'queue')
                ->all();

            $breakdown = [];
            foreach (\App\Support\QueueNames::all() as $queue) {
                $breakdown[$queue] = (int) ($rows[$queue] ?? 0);
            }

            foreach ($rows as $queue => $total) {
                if (! array_key_exists($queue, $breakdown)) {
                    $breakdown[$queue] = (int) $total;
                }
            }

            return $breakdown;
        } catch (\Exception $e) {
            return [];
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
        try {
            $free = disk_free_space(base_path());
            $total = disk_total_space(base_path());
            $usedPercent = round((($total - $free) / $total) * 100, 2);
            $warningThreshold = (int) config('observability.alerts.disk_warning_percent', 85);

            return [
                'status' => $usedPercent > $warningThreshold ? 'warning' : 'ok',
                'free' => $this->formatBytes($free),
                'total' => $this->formatBytes($total),
                'used_percent' => $usedPercent,
            ];
        } catch (\Exception $e) {
            return ['status' => 'unknown', 'message' => 'Disk check failed'];
        }
    }

    /**
     * Check CPU load.
     */
    public function checkCpu(): array
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->checkCpuWindows();
        }

        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                'status' => $load[0] > 2.0 ? 'warning' : 'ok',
                'load_1m' => $load[0],
                'load_5m' => $load[1],
                'load_15m' => $load[2],
            ];
        }

        return ['status' => 'unknown', 'message' => 'sys_getloadavg not available'];
    }

    protected function checkCpuWindows(): array
    {
        try {
            $load = $this->readWindowsCpuLoadPercent();
            if ($load !== null) {
                return [
                    'status' => $load > 85 ? 'warning' : 'ok',
                    'load_1m' => $load,
                    'message' => "Windows CPU Load: {$load}%",
                ];
            }
        } catch (\Exception $e) {
        }

        return ['status' => 'unknown'];
    }

    protected function readWindowsCpuLoadPercent(): ?float
    {
        $output = $this->runWindowsShell(
            'wmic cpu get loadpercentage /format:csv',
            '(Get-CimInstance Win32_Processor | Measure-Object -Property LoadPercentage -Average).Average'
        );
        if ($output === null || trim($output) === '') {
            return null;
        }

        if (is_numeric(trim($output))) {
            return (float) trim($output);
        }

        $lines = explode("\n", trim($output));
        foreach ($lines as $line) {
            if (empty(trim($line)) || str_contains($line, 'LoadPercentage')) {
                continue;
            }
            $data = str_getcsv($line);
            if (isset($data[1]) && is_numeric($data[1])) {
                return (float) $data[1];
            }
        }

        return null;
    }

    /**
     * Check memory usage.
     */
    public function checkMemory(): array
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->checkMemoryWindows();
        }

        try {
            $free = shell_exec('free');
            if (!$free) return ['status' => 'unknown'];
            
            $free = (string)trim($free);
            $free_arr = explode("\n", $free);
            if (count($free_arr) < 2) return ['status' => 'unknown'];
            
            $mem = explode(" ", $free_arr[1]);
            $mem = array_filter($mem);
            $mem = array_values($mem);
            
            if (count($mem) < 3) return ['status' => 'unknown'];
            
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

    protected function checkMemoryWindows(): array
    {
        try {
            $memory = $this->readWindowsMemoryStats();
            if ($memory !== null) {
                [$total, $free] = $memory;
                $used = $total - $free;
                $usedPercent = round(($used / $total) * 100, 2);

                return [
                    'status' => $usedPercent > 90 ? 'warning' : 'ok',
                    'used_percent' => $usedPercent,
                    'total' => $this->formatBytes($total),
                    'free' => $this->formatBytes($free),
                ];
            }
        } catch (\Exception $e) {
        }

        return ['status' => 'unknown'];
    }

    /**
     * @return array{0: float, 1: float}|null [total bytes, free bytes]
     */
    protected function readWindowsMemoryStats(): ?array
    {
        $output = $this->runWindowsShell(
            null,
            '& { $os = Get-CimInstance Win32_OperatingSystem; Write-Output (([string]($os.TotalVisibleMemorySize * 1024)) + [char]32 + ([string]($os.FreePhysicalMemory * 1024))) }'
        );
        if ($output !== null && preg_match('/^(\d+(?:\.\d+)?)\s+(\d+(?:\.\d+)?)/', trim($output), $matches)) {
            $total = (float) $matches[1];
            $free = (float) $matches[2];
            if ($total > 0) {
                return [$total, $free];
            }
        }

        if ($output !== null && str_contains($output, ',')) {
            [$total, $free] = array_map('floatval', explode(',', trim($output), 2));
            if ($total > 0) {
                return [$total, $free];
            }
        }

        $totalOutput = $this->runWindowsShell('wmic ComputerSystem get TotalPhysicalMemory /format:csv', null);
        $freeOutput = $this->runWindowsShell('wmic OS get FreePhysicalMemory /format:csv', null);

        $total = 0.0;
        $free = 0.0;

        if ($totalOutput) {
            $lines = explode("\n", trim($totalOutput));
            foreach ($lines as $line) {
                if (str_contains($line, 'TotalPhysicalMemory') || empty(trim($line))) {
                    continue;
                }
                $data = str_getcsv($line);
                if (isset($data[1])) {
                    $total = (float) $data[1];
                }
            }
        }

        if ($freeOutput) {
            $lines = explode("\n", trim($freeOutput));
            foreach ($lines as $line) {
                if (str_contains($line, 'FreePhysicalMemory') || empty(trim($line))) {
                    continue;
                }
                $data = str_getcsv($line);
                if (isset($data[1])) {
                    $free = (float) $data[1] * 1024;
                }
            }
        }

        return $total > 0 ? [$total, $free] : null;
    }

    protected function runWindowsShell(?string $wmicCommand, ?string $powershellCommand): ?string
    {
        if ($wmicCommand !== null) {
            $wmicPath = trim((string) shell_exec('where wmic 2>nul'));
            if ($wmicPath !== '') {
                $output = shell_exec($wmicCommand);
                if (is_string($output) && trim($output) !== '') {
                    return $output;
                }
            }
        }

        if ($powershellCommand !== null) {
            $output = shell_exec('powershell -NoProfile -Command '.escapeshellarg($powershellCommand));

            return is_string($output) ? $output : null;
        }

        return null;
    }

    /**
     * Determine overall system status.
     */
    private function determineOverallStatus(array $health): string
    {
        if ($health['database']['status'] === 'fail') return 'critical';
        
        foreach ($health as $key => $component) {
            if ($key === 'status') continue;
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
