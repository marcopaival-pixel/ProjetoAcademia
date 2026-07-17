<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\Plan;
use App\Models\FinancialLog;
use App\Services\FinancialLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Atualiza a forma de pagamento e registra o histórico.
     */
    public function updatePaymentMethod(Subscription $subscription, array $data)
    {
        return DB::transaction(function () use ($subscription, $data) {
            $oldMethod = $subscription->payment_method;
            
            $subscription->update([
                'payment_method' => $data['method'],
                'card_brand' => $data['card_brand'] ?? null,
                'card_last_four' => $data['card_last_four'] ?? null,
                'card_expiry' => $data['card_expiry'] ?? null,
            ]);

            $this->logEvent($subscription, 'payment_method_updated', $subscription->status, $subscription->status, null, [
                'old_method' => $oldMethod,
                'new_method' => $data['method']
            ]);

            return $subscription;
        });
    }

    /**
     * Processa tentativa de cobrança com lógica de retry (0, 1, 3, 5 dias).
     */
    public function processPaymentAttempt(Subscription $subscription)
    {
        $subscription->last_attempt_at = now();
        $subscription->retry_count++;
        
        // Simulação de tentativa de cobrança via Gateway
        $subscription->save();

        // Simulação de chamada ao Gateway (Mercado Pago etc.)
        $success = false; // Simulação de falha para testar retry

        if ($success) {
            return $this->reactivate($subscription, 'Pagamento confirmado via gateway.');
        }

        // Lógica de Retries: 0, 1, 3, 5
        $retries = [0, 1, 3, 5];
        $maxRetries = count($retries) - 1;

        if ($subscription->retry_count > $maxRetries) {
            return $this->suspend($subscription, 'Todas as tentativas de cobrança falharam.');
        }

        // Define próxima tentativa baseada no array de retries
        $daysToWait = $retries[$subscription->retry_count] ?? 0;
        $subscription->next_billing_date = now()->addDays($daysToWait);
        
        $oldStatus = $subscription->status;
        $subscription->status = Subscription::FIN_ATRASADO;
        $subscription->days_overdue = now()->diffInDays($subscription->last_attempt_at);
        $subscription->save();

        $this->logEvent($subscription, 'payment_failure', $oldStatus, $subscription->status, null, [
            'retry_count' => $subscription->retry_count,
            'next_attempt' => $subscription->next_billing_date
        ]);

        FinancialLogService::log([
            'user_id' => $subscription->user_id,
            'academy_company_id' => $subscription->academy_company_id,
            'action' => 'PAYMENT_FAILURE',
            'status_before' => $oldStatus,
            'status_after' => $subscription->status,
            'observation' => "Falha na tentativa de cobrança #{$subscription->retry_count}",
            'payload' => ['retry_count' => $subscription->retry_count]
        ]);

        return $subscription;
    }

    /**
     * Suspende a assinatura e bloqueia o acesso premium.
     */
    public function suspend(Subscription $subscription, string $reason = '')
    {
        $oldStatus = $subscription->status;
        $subscription->update([
            'status' => Subscription::FIN_SUSPENSO,
            'reason_for_suspension' => $reason
        ]);

        $this->logEvent($subscription, 'suspended', $oldStatus, Subscription::FIN_SUSPENSO, null, ['reason' => $reason]);
        
        FinancialLogService::log([
            'user_id' => $subscription->user_id,
            'academy_company_id' => $subscription->academy_company_id,
            'action' => 'SUSPENSION',
            'status_before' => $oldStatus,
            'status_after' => Subscription::FIN_SUSPENSO,
            'observation' => $reason
        ]);

        return $subscription;
    }

    /**
     * Reativa a assinatura.
     */
    public function reactivate(Subscription $subscription, string $notes = '')
    {
        $oldStatus = $subscription->status;
        $subscription->update([
            'status' => Subscription::FIN_ATIVO,
            'retry_count' => 0,
            'days_overdue' => 0,
            'reason_for_suspension' => null,
            'next_billing_date' => now()->addMonth(), // Ciclo mensal padrão
            'end_date' => now()->addMonth()
        ]);

        $this->logEvent($subscription, 'reactivated', $oldStatus, Subscription::FIN_ATIVO, null, ['notes' => $notes]);
        
        FinancialLogService::log([
            'user_id' => $subscription->user_id,
            'academy_company_id' => $subscription->academy_company_id,
            'action' => 'REACTIVATION',
            'status_before' => $oldStatus,
            'status_after' => Subscription::FIN_ATIVO,
            'observation' => $notes
        ]);

        return $subscription;
    }

    /**
     * Realiza upgrade imediato pagando a diferença.
     */
    public function upgrade(Subscription $subscription, Plan $newPlan)
    {
        return DB::transaction(function () use ($subscription, $newPlan) {
            $oldPlan = $subscription->plan;
            $diff = $newPlan->price - $oldPlan->price;

            // Aqui geraria uma cobrança imediata de $diff no gateway
            
            $subscription->update([
                'plan_id' => $newPlan->id,
                'status' => Subscription::FIN_ATIVO,
                'days_overdue' => 0
            ]);

            $this->logEvent($subscription, 'upgrade', $subscription->status, Subscription::FIN_ATIVO, $diff, [
                'from_plan' => $oldPlan->name,
                'to_plan' => $newPlan->name
            ]);

            FinancialLogService::log([
                'user_id' => $subscription->user_id,
                'academy_company_id' => $subscription->academy_company_id,
                'action' => 'UPGRADE',
                'amount' => $diff,
                'payload' => ['from_plan' => $oldPlan->name, 'to_plan' => $newPlan->name]
            ]);

            return $subscription;
        });
    }

    /**
     * Agenda downgrade para o próximo ciclo.
     */
    public function downgrade(Subscription $subscription, Plan $newPlan)
    {
        $subscription->update([
            'pending_plan_id' => $newPlan->id,
            'status' => Subscription::STATUS_CANCELLED_SCHEDULED
        ]);

        $this->logEvent($subscription, 'downgrade_scheduled', $subscription->status, Subscription::STATUS_CANCELLED_SCHEDULED, null, [
            'to_plan' => $newPlan->name
        ]);

        return $subscription;
    }

    /**
     * Cancela a assinatura mas mantém acesso até o fim do período.
     */
    public function cancel(Subscription $subscription)
    {
        $subscription->update([
            'status' => Subscription::STATUS_CANCELLED_SCHEDULED,
            'cancelled_at' => now()
        ]);

        $this->logEvent($subscription, 'cancelled', Subscription::STATUS_ACTIVE, Subscription::STATUS_CANCELLED_SCHEDULED);
        
        return $subscription;
    }

    /**
     * Bloqueia a assinatura por inadimplência grave (15+ dias).
     */
    public function block(Subscription $subscription, string $reason = '')
    {
        $oldStatus = $subscription->status;
        $subscription->update([
            'status' => Subscription::FIN_BLOQUEADO,
            'reason_for_suspension' => $reason
        ]);

        $this->logEvent($subscription, 'blocked', $oldStatus, Subscription::FIN_BLOQUEADO, null, ['reason' => $reason]);

        FinancialLogService::log([
            'user_id' => $subscription->user_id,
            'academy_company_id' => $subscription->academy_company_id,
            'action' => 'BLOCK',
            'status_before' => $oldStatus,
            'status_after' => Subscription::FIN_BLOQUEADO,
            'observation' => $reason
        ]);

        return $subscription;
    }

    /**
     * Processa reembolso.
     */
    public function refund(Subscription $subscription, float $amount, bool $full = true)
    {
        $subscription->update([
            'status' => $full ? Subscription::STATUS_CANCELLED : $subscription->status,
            'refunded_at' => now(),
            'refunded_amount' => $amount
        ]);

        $this->logEvent($subscription, 'refund', $subscription->status, $subscription->status, $amount, [
            'type' => $full ? 'full' : 'partial'
        ]);

        return $subscription;
    }

    /**
     * Atalho para registrar logs.
     */
    private function logEvent(Subscription $subscription, string $event, $oldStatus, $newStatus, $amount = null, array $payload = [])
    {
        return SubscriptionLog::create([
            'subscription_id' => $subscription->id,
            'event' => $event,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'amount' => $amount,
            'payload' => $payload,
        ]);
    }
}
