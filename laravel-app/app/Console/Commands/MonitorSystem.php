<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Operations\SystemHealthService;
use App\Services\Operations\OperationalAlertService;
use App\Notifications\OperationalAlert;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitorSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa verificações de saúde do sistema e dispara alertas se necessário.';

    protected $healthService;
    protected $alertService;

    public function __construct(SystemHealthService $healthService, OperationalAlertService $alertService)
    {
        parent::__construct();
        $this->healthService = $healthService;
        $this->alertService = $alertService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando monitoramento do sistema...');
        
        $health = $this->healthService->checkAll();
        
        // Log results
        Log::channel('single')->info('System Health Check Results', $health);

        if ($health['status'] !== 'healthy') {
            $this->error('Problemas detectados: ' . $health['status']);
            $this->processAlerts($health);
            $this->attemptAutoRecovery($health);
        } else {
            $this->info('Sistema saudável.');
        }

        return 0;
    }

    /**
     * Process and send alerts.
     */
    protected function processAlerts(array $health)
    {
        $criticalComponents = [];
        
        if ($health['database']['status'] === 'fail') $criticalComponents[] = 'Banco de Dados';
        if ($health['queue']['status'] === 'fail') $criticalComponents[] = 'Fila (Queue)';
        if ($health['cache']['status'] === 'fail') $criticalComponents[] = 'Cache';
        if ($health['disk']['status'] === 'warning') $criticalComponents[] = 'Espaço em Disco Baixo';

        if (empty($criticalComponents)) return;

        $message = 'Detectamos problemas nos seguintes componentes: ' . implode(', ', $criticalComponents);

        if ($health['database']['status'] === 'fail') {
            $this->alertService->sendEmergencyEmail(
                'Urgente: Banco de dados indisponível',
                $message,
                $health
            );
            return;
        }

        // Deduplication: don't alert for the same component more than once every 30 minutes
        $cacheKey = 'alert_sent_' . md5(implode(',', $criticalComponents));
        try {
            if (Cache::has($cacheKey)) {
                $this->info('Alerta já enviado recentemente. Ignorando para evitar spam.');
                return;
            }
        } catch (\Throwable $e) {
            Log::warning('Não foi possível consultar deduplicação de alerta: ' . $e->getMessage());
        }

        try {
            $admins = User::where('is_admin', true)->get();
        } catch (\Throwable $e) {
            Log::error('Falha ao buscar administradores para alerta operacional: ' . $e->getMessage());
            $this->alertService->sendEmergencyEmail('Urgente: Falha no Sistema', $message, $health);

            return;
        }

        if ($admins->isEmpty()) {
            $this->alertService->sendEmergencyEmail('Urgente: Falha no Sistema', $message, $health);

            return;
        }
        
        foreach ($admins as $admin) {
            $admin->notify(new OperationalAlert([
                'title' => 'Urgente: Falha no Sistema',
                'message' => $message,
                'component' => implode('|', $criticalComponents),
                'level' => 'critical',
                'action_url' => url('/admin/operations')
            ]));

            // Placeholder para Alerta WhatsApp (Ex: via Evolution API ou Twilio)
            $this->sendWhatsAppAlert($admin, 'Urgente: Falha no Sistema NexShape detectada nos componentes: ' . implode(', ', $criticalComponents));
        }

        try {
            Cache::put($cacheKey, true, now()->addMinutes(30));
        } catch (\Throwable $e) {
            Log::warning('Não foi possível gravar deduplicação de alerta: ' . $e->getMessage());
        }
        $this->info('Alertas enviados aos administradores.');
    }

    /**
     * Placeholder para integração com WhatsApp.
     */
    protected function sendWhatsAppAlert($user, $message)
    {
        if (!$user->phone) return;

        // Log da tentativa
        Log::info("WhatsApp alert would be sent to {$user->phone}: {$message}");
        
        // Aqui seria a integração real:
        // Http::post('https://seu-servidor-whatsapp/message', [
        //     'number' => $user->phone,
        //     'text' => $message
        // ]);
    }

    /**
     * Attempt to recover failed services.
     */
    protected function attemptAutoRecovery(array $health)
    {
        if ($health['queue']['status'] === 'fail') {
            $this->warn('Tentando reiniciar a fila...');
            try {
                \Illuminate\Support\Facades\Artisan::call('queue:restart');
                Log::info('Auto-recovery: Comando queue:restart executado.');
            } catch (\Exception $e) {
                Log::error('Auto-recovery falhou ao reiniciar fila: ' . $e->getMessage());
            }
        }

        // Database recovery is usually handled by the DB server itself, 
        // but we could trigger a custom script if running in a controlled environment.
    }
}
