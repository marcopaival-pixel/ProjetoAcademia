<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\Dunning\PaymentFailedMail;
use App\Mail\Dunning\SubscriptionSuspendedMail;

class ProcessDunning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:dunning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica assinaturas em atraso e processa a régua de cobrança (Dunning)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando o processo de Dunning...');

        // 1. Assinaturas em atraso (falha recente de cobrança)
        $overdueSubscriptions = Subscription::where('status', Subscription::FIN_ATRASADO)
            ->whereNotNull('user_id')
            ->get();

        foreach ($overdueSubscriptions as $subscription) {
            if (!$subscription->user) continue;

            // Dependendo dos dias de atraso, dispara e-mails diferentes
            $daysOverdue = $subscription->days_overdue;

            if ($daysOverdue == 1 || $daysOverdue == 3) {
                // Alerta de falha no cartão
                Mail::to($subscription->user->email)->queue(new PaymentFailedMail($subscription->user, $daysOverdue));
                Log::info("Dunning: Enviado PaymentFailedMail para usuário {$subscription->user_id} (Atraso: {$daysOverdue} dias)");
            }
            
            // Suspensão no 5º dia
            if ($daysOverdue >= 5 && $subscription->retry_count >= 3) {
                // Suspender a assinatura no banco de dados (idealmente via SubscriptionService)
                app(\App\Services\SubscriptionService::class)->suspend($subscription, 'Inadimplência não resolvida após 5 dias');
                
                // Enviar aviso de suspensão
                Mail::to($subscription->user->email)->queue(new SubscriptionSuspendedMail($subscription->user));
                Log::info("Dunning: Assinatura suspensa e e-mail enviado para usuário {$subscription->user_id}");
            }
        }

        $this->info('Processo de Dunning concluído com sucesso!');
        return 0;
    }
}
