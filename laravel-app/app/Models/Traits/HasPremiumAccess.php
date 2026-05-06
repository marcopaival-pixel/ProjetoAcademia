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
        if ($this->isPremiumCache !== null) {
            return $this->isPremiumCache;
        }

        // Administradores e Profissionais têm acesso total
        if ($this->is_admin || $this->hasRole('professional')) {
            return $this->isPremiumCache = true;
        }

        // 1. Verificar nova estrutura de Assinatura (SaaS Premium)
        $subscription = $this->relationLoaded('currentSubscription') ? $this->currentSubscription : $this->currentSubscription()->first();
        if ($subscription) {
            if ($subscription->status === \App\Models\Subscription::STATUS_FIN_ATIVO) {
                return $this->isPremiumCache = true;
            }
            
            // Se houver uma assinatura mas não estiver ATIVA, bloqueamos explicitamente
            if (in_array($subscription->status, [\App\Models\Subscription::STATUS_FIN_PENDENTE, \App\Models\Subscription::STATUS_FIN_AGUARDANDO, \App\Models\Subscription::STATUS_FIN_RECUSADO])) {
                return $this->isPremiumCache = false;
            }
        }

        // 2. Verificar Assinatura Corporativa (B2B)
        if ($this->academy_company_id) {
            $company = $this->academyCompany;
            if ($company) {
                $corporateSub = $company->subscriptions()
                    ->where('status', 'active')
                    ->where('billing_type', 'corporate')
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now()->toDateString());
                    })
                    ->exists();
                
                if ($corporateSub) {
                    return $this->isPremiumCache = true;
                }
            }
        }

        // 3. Sistema de planos (PRO) - Legado ou Alunos
        $activePlan = $this->relationLoaded('activePlan') ? $this->activePlan : $this->activePlan()->first();
        
        if ($activePlan && trim(strtoupper($activePlan->plan->name)) === 'PRO' && $activePlan->status === 'active') {
            return $this->isPremiumCache = true;
        }

        // Legado (campo is_premium direto no users)
        if ($this->is_premium) {
            $exp = $this->premium_expires_at;
            if ($exp === null) {
                return $this->isPremiumCache = true;
            }
            return $this->isPremiumCache = ($exp >= now());
        }

        return $this->isPremiumCache = false;
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
