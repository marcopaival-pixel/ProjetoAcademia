<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Services\FinancialLogService;

class FinancialStatusCheck extends Command
{
    protected $signature = 'financial:check-status';
    protected $description = 'Verifica e atualiza o status financeiro baseado nos dias de atraso (5, 10, 15 dias).';

    public function handle(SubscriptionService $service)
    {
        $overdueSubscriptions = Subscription::whereIn('status', [
                Subscription::FIN_ATRASADO,
                Subscription::STATUS_OVERDUE,
                Subscription::FIN_PENDENTE,
                Subscription::STATUS_PENDING
            ])
            ->whereNotNull('last_attempt_at')
            ->get();

        foreach ($overdueSubscriptions as $sub) {
            $days = now()->diffInDays($sub->last_attempt_at);
            $sub->days_overdue = $days;
            
            $oldStatus = $sub->status;

            if ($days >= 15 && $sub->status !== Subscription::FIN_BLOQUEADO) {
                $service->block($sub, 'Bloqueio automático por 15+ dias de inadimplência.');
                $this->warn("Assinatura #{$sub->id} BLOQUEADA (15+ dias)");
            } elseif ($days >= 10 && $sub->status !== Subscription::FIN_SUSPENSO && $sub->status !== Subscription::FIN_BLOQUEADO) {
                $service->suspend($sub, 'Suspensão automática por 10+ dias de inadimplência.');
                $this->info("Assinatura #{$sub->id} SUSPENSA (10+ dias)");
            } elseif ($days >= 5 && $sub->status !== Subscription::FIN_ATRASADO && $sub->status !== Subscription::FIN_SUSPENSO && $sub->status !== Subscription::FIN_BLOQUEADO) {
                $sub->status = Subscription::FIN_ATRASADO;
                $sub->save();
                
                FinancialLogService::log([
                    'user_id' => $sub->user_id,
                    'action' => 'STATUS_AUTO_UPDATE',
                    'status_before' => $oldStatus,
                    'status_after' => Subscription::FIN_ATRASADO,
                    'observation' => 'Atualização automática para ATRASADO (5+ dias)'
                ]);
                $this->info("Assinatura #{$sub->id} marcada como ATRASADA (5+ dias)");
            } else {
                $sub->save(); // Apenas atualiza days_overdue
            }
        }

        $this->info('Verificação de status financeiro concluída.');
    }
}
