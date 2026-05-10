<?php

namespace App\Services\Operations;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class WorkerManagementService
{
    protected $lockKey = 'worker_restart_lock';

    /**
     * Restart all workers.
     */
    public function restartAll(): array
    {
        if (!$this->acquireLock()) {
            return ['status' => 'error', 'message' => 'Um reinício já está em andamento.'];
        }

        try {
            Artisan::call('queue:restart');
            $this->logAudit('Reinício global de workers solicitado.');
            return ['status' => 'success', 'message' => 'Comando de reinício enviado para todos os workers.'];
        } catch (\Exception $e) {
            Log::error('Erro ao reiniciar workers: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Falha ao enviar comando de reinício.'];
        } finally {
            $this->releaseLock();
        }
    }

    /**
     * Clear a specific queue.
     */
    public function clearQueue(string $queue = 'default'): array
    {
        try {
            Artisan::call('queue:clear', ['--queue' => $queue, '--force' => true]);
            $this->logAudit("Fila '{$queue}' limpa.");
            return ['status' => 'success', 'message' => "Fila '{$queue}' limpa com sucesso."];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao limpar fila: ' . $e->getMessage()];
        }
    }

    /**
     * Retry failed jobs.
     */
    public function retryFailedJobs(string $id = 'all'): array
    {
        try {
            Artisan::call('queue:retry', ['id' => [$id]]);
            $this->logAudit("Tentativa de reprocessamento de jobs falhados: {$id}.");
            return ['status' => 'success', 'message' => 'Jobs enviados para reprocessamento.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao reprocessar jobs: ' . $e->getMessage()];
        }
    }

    /**
     * Flush all failed jobs.
     */
    public function flushFailedJobs(): array
    {
        try {
            Artisan::call('queue:flush');
            $this->logAudit('Todos os jobs falhados foram removidos.');
            return ['status' => 'success', 'message' => 'Histórico de falhas limpo.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao limpar jobs falhados: ' . $e->getMessage()];
        }
    }

    /**
     * Get active workers (Windows specific logic).
     */
    public function getActiveWorkers(): array
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->getWindowsWorkers();
        }

        return $this->getLinuxWorkers();
    }

    protected function getWindowsWorkers(): array
    {
        try {
            // Using WMIC to find PHP processes running queue commands
            $output = shell_exec('wmic process where "name=\'php.exe\'" get commandline,processid /format:csv');
            if (!$output) return [];

            $lines = explode("\n", trim($output));
            $workers = [];

            foreach ($lines as $line) {
                if (empty(trim($line)) || str_contains($line, 'Node.js') || str_contains($line, 'ProcessId')) continue;
                
                $data = str_getcsv($line);
                if (count($data) < 3) continue;

                $commandLine = $data[1];
                $pid = $data[2];

                if (str_contains($commandLine, 'artisan queue:work') || str_contains($commandLine, 'artisan queue:listen')) {
                    $workers[] = [
                        'pid' => $pid,
                        'command' => trim($commandLine),
                        'type' => str_contains($commandLine, 'listen') ? 'Listen' : 'Work',
                        'status' => 'active',
                        'memory' => $this->getProcessMemoryWindows($pid)
                    ];
                }
            }

            return $workers;
        } catch (\Exception $e) {
            Log::warning('Falha ao listar workers Windows: ' . $e->getMessage());
            return [];
        }
    }

    protected function getLinuxWorkers(): array
    {
        try {
            $output = shell_exec('ps aux | grep "artisan queue" | grep -v grep');
            if (!$output) return [];

            $lines = explode("\n", trim($output));
            $workers = [];

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                $parts = preg_split('/\s+/', $line);
                if (count($parts) < 11) continue;

                $workers[] = [
                    'pid' => $parts[1],
                    'user' => $parts[0],
                    'cpu' => $parts[2] . '%',
                    'mem' => $parts[3] . '%',
                    'command' => implode(' ', array_slice($parts, 10)),
                    'status' => 'active'
                ];
            }

            return $workers;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getProcessMemoryWindows($pid): string
    {
        $output = shell_exec("wmic process where processid=$pid get WorkingSetSize /format:csv");
        if (!$output) return 'N/A';
        
        $lines = explode("\n", trim($output));
        foreach ($lines as $line) {
            if (empty(trim($line)) || str_contains($line, 'WorkingSetSize')) continue;
            $data = str_getcsv($line);
            if (isset($data[1])) {
                return round($data[1] / 1024 / 1024, 2) . ' MB';
            }
        }
        return 'N/A';
    }

    protected function logAudit(string $action): void
    {
        Log::channel('single')->info("OPERATIONS_AUDIT: " . $action, [
            'user_id' => auth()->id() ?? 'system',
            'ip' => request()->ip() ?? '127.0.0.1',
            'timestamp' => now()->toIso8601String()
        ]);
    }

    protected function acquireLock(): bool
    {
        return Cache::add($this->lockKey, true, 60);
    }

    protected function releaseLock(): void
    {
        Cache::forget($this->lockKey);
    }
}
