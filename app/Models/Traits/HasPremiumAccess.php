<?php

namespace App\Models\Traits;

use DateTimeImmutable;

trait HasPremiumAccess
{
    /**
     * Cache estático para evitar múltiplas consultas ao BD na mesma requisição.
     */
    protected ?bool $isPremiumCache = null;

    public function isPremiumActive(): bool
    {
        // 1. Administradores e Staff têm acesso total por definição
        if ($this->is_admin || $this->hasRole(['professional', 'manager', 'instructor', 'supervisor', 'receptionist'])) {
            return true;
        }

        // 2. Verificar campo direto is_premium (Legado/Atalhos)
        if ($this->is_premium) {
            if ($this->premium_expires_at === null || $this->premium_expires_at >= now()) {
                return true;
            }
        }

        // 3. Verificar Assinatura (SaaS)
        $subscription = $this->relationLoaded('currentSubscription') ? $this->currentSubscription : $this->currentSubscription()->first();
        if ($subscription && method_exists($subscription, 'isActive') && $subscription->isActive()) {
            return true;
        }

        // 4. Verificar Plano Ativo (Alunos)
        $activePlan = $this->relationLoaded('activePlan') ? $this->activePlan : $this->activePlan()->first();
        if ($activePlan && $activePlan->status === 'active') {
            $plan = $activePlan->plan;
            if ($plan && (strtoupper($plan->name) !== 'FREE' || $plan->price > 0)) {
                return true;
            }
        }

        // 5. Fallback direto pelo plan_id no User
        if ($this->plan_id) {
            $plan = $this->relationLoaded('plan') ? $this->plan : $this->plan()->first();
            if ($plan && (strtoupper($plan->name) !== 'FREE' || $plan->price > 0)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Acesso às funcionalidades reservadas ao Premium (export CSV, macros manuais, chat IA sem quota, etc.).
     * Administradores têm o mesmo acesso sem necessidade de assinatura.
     */
    public function hasPremiumAccess(): bool
    {
        return $this->isPremiumActive();
    }

    public function activePlanType(): ?string
    {
        $subscription = $this->currentSubscription;
        return $subscription ? $subscription->plan->type : null;
    }

    public function hasPlanFeature(string $feature): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        $subscription = $this->currentSubscription;
        if (!$subscription) {
            // Fallback para UserPlan (sistema de Alunos)
            $activePlan = $this->activePlan;
            if ($activePlan) {
                return $activePlan->plan->hasFeature($feature);
            }
            return false;
        }

        return $subscription->plan->hasFeature($feature);
    }
}
