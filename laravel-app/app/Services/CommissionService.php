<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Payment;
use App\Models\ReferralCode;
use App\Models\Clinic;
use App\Models\RepresentativeAudit;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Comissão definitiva após confirmação de pagamento (idempotente por payment_id).
     */
    public function recordOnPayment(User $user, Payment $payment, ?Subscription $subscription = null, ?float $baseAmountOverride = null): ?Commission
    {
        if ($subscription === null) {
            return null;
        }

        $this->finalizeReferralAfterPayment($user);

        $representativeId = $user->representative_id;
        if (! $representativeId && $user->clinic_id) {
            $representativeId = Clinic::where('id', $user->clinic_id)->value('representative_id');
        }
        if (! $representativeId) {
            return null;
        }

        $existing = Commission::where('payment_id', $payment->id)->first();
        if ($existing) {
            return $existing;
        }

        $this->cancelSupersededCommissions($representativeId, $user->id, $subscription?->id);

        $rate = $this->resolveRate($user, $subscription);
        if ($rate <= 0) {
            return null;
        }

        $baseAmount = $baseAmountOverride ?? (float) $payment->amount;
        $commissionAmount = round(($baseAmount * $rate) / 100, 2);

        $commission = Commission::create([
            'representative_id' => $representativeId,
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'subscription_id' => $subscription?->id,
            'base_amount' => $baseAmount,
            'commission_rate' => $rate,
            'commission_amount' => $commissionAmount,
            'status' => Commission::STATUS_PENDENTE,
            'available_at' => now()->addDays(7),
            'notes' => 'Comissão gerada automaticamente via pagamento '.$payment->gateway_id,
        ]);

        $this->auditCommissionGenerated($representativeId, $commission, $baseAmount, $rate, $commissionAmount);

        app(CommissionMetricsService::class)->clearCache();

        return $commission;
    }

    /**
     * Comissão provisória no checkout, antes da confirmação do gateway.
     */
    public function recordAwaitingPayment(
        int $representativeId,
        int $userId,
        ?int $subscriptionId,
        ?int $clinicId,
        float $baseAmount,
        float $rate,
        float $commissionAmount,
        string $notes = ''
    ): Commission {
        $this->cancelAwaitingForSubscription($representativeId, $userId, $subscriptionId);

        return Commission::create([
            'representative_id' => $representativeId,
            'user_id' => $userId,
            'clinic_id' => $clinicId,
            'subscription_id' => $subscriptionId,
            'payment_id' => null,
            'base_amount' => $baseAmount,
            'commission_rate' => $rate,
            'commission_type' => 'percentual',
            'commission_amount' => round($commissionAmount, 2),
            'status' => Commission::STATUS_AGUARDANDO_PAGAMENTO,
            'notes' => $notes,
        ]);
    }

    /**
     * Comissão prevista no cadastro (sem pagamento ainda).
     */
    public function recordProspectiveOnRegistration(
        int $representativeId,
        int $userId,
        float $baseAmount,
        float $rate,
        string $notes = ''
    ): ?Commission {
        if ($baseAmount <= 0 || $rate <= 0) {
            return null;
        }

        $duplicate = Commission::where('representative_id', $representativeId)
            ->where('user_id', $userId)
            ->whereNull('payment_id')
            ->whereIn('status', [Commission::STATUS_PENDENTE, Commission::STATUS_AGUARDANDO_PAGAMENTO])
            ->exists();

        if ($duplicate) {
            return null;
        }

        return Commission::create([
            'representative_id' => $representativeId,
            'user_id' => $userId,
            'payment_id' => null,
            'base_amount' => $baseAmount,
            'commission_rate' => $rate,
            'commission_amount' => round(($baseAmount * $rate) / 100, 2),
            'status' => Commission::STATUS_PENDENTE,
            'notes' => $notes,
        ]);
    }

    /**
     * Libera comissões cuja carência (available_at) já expirou.
     */
    public function releaseAvailableCommissions(): int
    {
        $ids = Commission::query()
            ->where('status', Commission::STATUS_PENDENTE)
            ->whereNotNull('available_at')
            ->where('available_at', '<=', now())
            ->pluck('id');

        if ($ids->isEmpty()) {
            return 0;
        }

        $commissions = Commission::whereIn('id', $ids)->get();

        Commission::whereIn('id', $ids)->update(['status' => Commission::STATUS_DISPONIVEL]);

        foreach ($commissions as $commission) {
            RepresentativeAudit::create([
                'user_id' => $commission->representative_id,
                'action' => 'commission_released',
                'entity_type' => Commission::class,
                'entity_id' => $commission->id,
                'new_values' => ['status' => Commission::STATUS_DISPONIVEL],
            ]);
        }

        if ($commissions->count() > 0) {
            app(CommissionMetricsService::class)->clearCache();
            app(ExecutiveDashboardService::class)->clearCache();
        }

        return $commissions->count();
    }

    private function cancelSupersededCommissions(int $representativeId, int $userId, ?int $subscriptionId): void
    {
        $query = Commission::where('representative_id', $representativeId)
            ->where('user_id', $userId)
            ->whereNull('payment_id')
            ->whereIn('status', [
                Commission::STATUS_AGUARDANDO_PAGAMENTO,
                Commission::STATUS_PENDENTE,
            ]);

        if ($subscriptionId !== null) {
            $query->where(function ($q) use ($subscriptionId) {
                $q->where('subscription_id', $subscriptionId)
                    ->orWhereNull('subscription_id');
            });
        }

        $query->get()->each(function (Commission $commission) {
            $commission->update([
                'status' => Commission::STATUS_CANCELADO,
                'notes' => trim(($commission->notes ?? '').' [Cancelada: pagamento confirmado]'),
            ]);
        });
    }

    private function cancelAwaitingForSubscription(int $representativeId, int $userId, ?int $subscriptionId): void
    {
        if ($subscriptionId === null) {
            return;
        }

        Commission::where('representative_id', $representativeId)
            ->where('user_id', $userId)
            ->where('subscription_id', $subscriptionId)
            ->where('status', Commission::STATUS_AGUARDANDO_PAGAMENTO)
            ->whereNull('payment_id')
            ->get()
            ->each(function (Commission $commission) {
                $commission->update([
                    'status' => Commission::STATUS_CANCELADO,
                    'notes' => trim(($commission->notes ?? '').' [Substituída por nova tentativa de checkout]'),
                ]);
            });
    }

    private function resolveRate(User $user, ?Subscription $subscription): float
    {
        $rate = 0.0;

        if ($subscription && $subscription->plan) {
            $rate = (float) $subscription->plan->commission_rate;
        }

        $representativeUser = User::with('representativeProfile')->find($user->representative_id);

        if ($rate <= 0 && $representativeUser?->representativeProfile) {
            $rate = (float) $representativeUser->representativeProfile->commission_rate;
        }

        if ($rate <= 0) {
            $rate = (float) config('projeto.default_commission_rate', 10.00);
        }

        return $rate;
    }

    private function auditCommissionGenerated(
        int $representativeId,
        Commission $commission,
        float $baseAmount,
        float $rate,
        float $commissionAmount
    ): void {
        RepresentativeAudit::create([
            'user_id' => $representativeId,
            'action' => 'commission_generated',
            'entity_type' => Commission::class,
            'entity_id' => $commission->id,
            'new_values' => [
                'base_amount' => $baseAmount,
                'rate' => $rate,
                'commission_amount' => $commissionAmount,
            ],
        ]);

        Log::info('[Representative] Comissão gerada.', [
            'representative_id' => $representativeId,
            'commission_id' => $commission->id,
            'amount' => $commissionAmount,
        ]);
    }

    /**
     * Marca código de indicação como utilizado após confirmação de pagamento.
     */
    public function finalizeReferralAfterPayment(User $user): void
    {
        if (! $user->clinic_id) {
            return;
        }

        $clinic = Clinic::find($user->clinic_id);
        if (! $clinic || ! $clinic->representative_id || ! $clinic->representative_code_used) {
            return;
        }

        if (! $user->representative_id) {
            $user->representative_id = $clinic->representative_id;
            $user->save();
        }

        $referralCode = ReferralCode::where('code', $clinic->representative_code_used)->first();
        if ($referralCode && $referralCode->isValid()) {
            $referralCode->markAsUsed($user->clinic_id);
        }
    }
}
