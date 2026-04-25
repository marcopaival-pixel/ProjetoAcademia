<?php

namespace App\Models\Traits;

use DateTimeImmutable;

trait HasPremiumAccess
{
    public function isPremiumActive(): bool
    {
        // Administradores e Profissionais têm acesso total
        if ($this->isAdministrator() || $this->hasRole('professional')) {
            return true;
        }

        // 1. Verificar Assinatura Corporativa (B2B)
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
                    return true;
                }
            }
        }

        // 2. Novo sistema de planos (PRO)
        $activePlan = $this->activePlan;
        if ($activePlan && trim(strtoupper($activePlan->plan->name)) === 'PRO') {
            return true;
        }

        // Fallback para plan_id direto no usuário (Plano 2 = PRO)
        if ($this->plan_id == 2) {
            return true;
        }

        // Legado (campo is_premium direto no users)
        if ($this->is_premium) {
            $exp = $this->premium_expires_at;
            if ($exp === null) {
                return true;
            }
            return $exp >= now();
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
