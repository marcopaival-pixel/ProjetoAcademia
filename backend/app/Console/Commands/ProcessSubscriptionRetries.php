<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\SubscriptionService;

class ProcessSubscriptionRetries extends Command
{
    protected $signature = 'subscription:process-retries';
    protected $description = 'Processa as tentativas de cobrança para assinaturas em atraso.';

    public function handle(SubscriptionService $service)
    {
        $overdueSubscriptions = Subscription::whereIn('status', [
                Subscription::STATUS_OVERDUE, 
                Subscription::STATUS_PENDING,
                Subscription::FIN_ATRASADO,
                Subscription::FIN_PENDENTE
            ])
            ->where('next_billing_date', '<=', now())
            ->get();

        if ($overdueSubscriptions->isEmpty()) {
            $this->info('Nenhuma assinatura em atraso para processar hoje.');
            return;
        }

        $this->info("Processando {$overdueSubscriptions->count()} assinaturas...");

        foreach ($overdueSubscriptions as $subscription) {
            $this->comment("Processando assinatura #{$subscription->id} para o usuário {$subscription->user_id}");
            $service->processPaymentAttempt($subscription);
        }

        $this->info('Processamento concluído.');
    }
}
