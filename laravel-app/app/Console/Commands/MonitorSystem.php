<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\OperationalAlert;
use App\Services\Operations\AlertDispatcher;
use App\Services\Operations\OperationalAlertService;
use App\Services\Operations\SystemHealthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitorSystem extends Command
{
    protected $signature = 'system:monitor';

    protected $description = 'Executa verificações de saúde do sistema e dispara alertas se necessário.';

    public function __construct(
        protected SystemHealthService $healthService,
        protected OperationalAlertService $alertService,
        protected AlertDispatcher $alertDispatcher,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Iniciando monitoramento do sistema...');

        $health = $this->healthService->checkAll();

        Log::channel('single')->info('System Health Check Results', $health);

        if ($health['status'] !== 'healthy') {
            $this->error('Problemas detectados: '.$health['status']);
            $this->processAlerts($health);
            $this->attemptAutoRecovery($health);
        } else {
            $this->info('Sistema saudável.');
        }

        $this->checkJobThresholds($health);

        return self::SUCCESS;
    }

    protected function checkJobThresholds(array $health): void
    {
        $failedHour = (int) ($health['jobs']['failed_last_hour'] ?? 0);
        $failedThreshold = (int) config('observability.thresholds.failed_jobs_per_hour', 10);
        $pendingTotal = (int) ($health['jobs']['pending'] ?? 0);
        $pendingThreshold = (int) config('observability.thresholds.pending_jobs', 100);

        $warnings = [];
        if ($failedHour >= $failedThreshold) {
            $warnings[] = "Jobs falhados na última hora: {$failedHour}";
        }
        if ($pendingTotal >= $pendingThreshold) {
            $warnings[] = "Jobs pendentes: {$pendingTotal}";
        }

        if ($warnings === []) {
            return;
        }

        $this->alertDispatcher->dispatchCritical(
            'Alerta operacional: filas',
            implode('; ', $warnings),
            $health,
            'ops_queue_threshold_'.md5(implode('|', $warnings)),
        );
    }

    protected function processAlerts(array $health): void
    {
        $criticalComponents = [];

        if ($health['database']['status'] === 'fail') {
            $criticalComponents[] = 'Banco de Dados';
        }
        if ($health['queue']['status'] === 'fail') {
            $criticalComponents[] = 'Fila (Queue)';
        }
        if ($health['cache']['status'] === 'fail') {
            $criticalComponents[] = 'Cache';
        }
        if ($health['disk']['status'] === 'warning') {
            $criticalComponents[] = 'Espaço em Disco Baixo';
        }

        if ($criticalComponents === []) {
            return;
        }

        $message = 'Detectamos problemas nos seguintes componentes: '.implode(', ', $criticalComponents);
        $dedupeKey = 'alert_sent_'.md5(implode(',', $criticalComponents));

        if ($health['database']['status'] === 'fail') {
            $this->alertDispatcher->dispatchCritical(
                'Urgente: Banco de dados indisponível',
                $message,
                $health,
                $dedupeKey,
            );

            return;
        }

        try {
            if (Cache::has($dedupeKey)) {
                $this->info('Alerta já enviado recentemente. Ignorando para evitar spam.');

                return;
            }
        } catch (\Throwable $e) {
            Log::warning('Não foi possível consultar deduplicação de alerta: '.$e->getMessage());
        }

        $this->alertDispatcher->sendSlack('Urgente: Falha no Sistema', $message, $health);

        try {
            $admins = User::where('is_admin', true)->get();
        } catch (\Throwable $e) {
            Log::error('Falha ao buscar administradores para alerta operacional: '.$e->getMessage());
            $this->alertDispatcher->dispatchCritical('Urgente: Falha no Sistema', $message, $health, $dedupeKey);

            return;
        }

        if ($admins->isEmpty()) {
            $this->alertDispatcher->dispatchCritical('Urgente: Falha no Sistema', $message, $health, $dedupeKey);

            return;
        }

        foreach ($admins as $admin) {
            $admin->notify(new OperationalAlert([
                'title' => 'Urgente: Falha no Sistema',
                'message' => $message,
                'component' => implode('|', $criticalComponents),
                'level' => 'critical',
                'action_url' => url('/admin/operations'),
            ]));

            $this->alertDispatcher->sendWhatsApp(
                $admin->phone,
                'Urgente: Falha no Sistema NexShape detectada nos componentes: '.implode(', ', $criticalComponents),
            );
        }

        try {
            Cache::put($dedupeKey, true, now()->addMinutes((int) config('observability.alerts.dedupe_minutes', 30)));
        } catch (\Throwable $e) {
            Log::warning('Não foi possível gravar deduplicação de alerta: '.$e->getMessage());
        }

        $this->info('Alertas enviados aos administradores.');
    }

    protected function attemptAutoRecovery(array $health): void
    {
        if ($health['queue']['status'] === 'fail') {
            $this->warn('Tentando reiniciar a fila...');
            try {
                Artisan::call('queue:restart');
                Log::info('Auto-recovery: Comando queue:restart executado.');
            } catch (\Exception $e) {
                Log::error('Auto-recovery falhou ao reiniciar fila: '.$e->getMessage());
            }
        }
    }
}
