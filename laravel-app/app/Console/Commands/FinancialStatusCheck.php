<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Services\FinancialLogService;
use App\Support\SubscriptionStatus;

class FinancialStatusCheck extends Command
{
    protected $signature = 'financial:check-status';
    protected $description = 'Verifica e atualiza o status financeiro baseado nos dias de atraso (5, 10, 15 dias).';

    public function handle(SubscriptionService $service)
    {
        $overdueSubscriptions = Subscription::whereCanonicalStatus(
            SubscriptionStatus::OVERDUE,
            SubscriptionStatus::PENDING
        )
            ->whereNotNull('last_attempt_at')
            ->get();

        foreach ($overdueSubscriptions as $sub) {
            $days = now()->diffInDays($sub->last_attempt_at);
            $sub->days_overdue = $days;
            
            $oldStatus = $sub->status;

            if ($days >= 15 && $sub->canonicalStatus() !== SubscriptionStatus::BLOCKED) {
                $service->block($sub, 'Bloqueio automático por 15+ dias de inadimplência.');
                $this->warn("Assinatura #{$sub->id} BLOQUEADA (15+ dias)");
            } elseif ($days >= 10 && ! in_array($sub->canonicalStatus(), [SubscriptionStatus::SUSPENDED, SubscriptionStatus::BLOCKED], true)) {
                $service->suspend($sub, 'Suspensão automática por 10+ dias de inadimplência.');
                $this->info("Assinatura #{$sub->id} SUSPENSA (10+ dias)");
            } elseif ($days >= 5 && ! in_array($sub->canonicalStatus(), [SubscriptionStatus::OVERDUE, SubscriptionStatus::SUSPENDED, SubscriptionStatus::BLOCKED], true)) {
                $sub->status = SubscriptionStatus::OVERDUE;
                $sub->save();

                FinancialLogService::log([
                    'user_id' => $sub->user_id,
                    'action' => 'STATUS_AUTO_UPDATE',
                    'status_before' => $oldStatus,
                    'status_after' => SubscriptionStatus::OVERDUE,
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
