<?php

namespace App\Services;

use App\Models\AppFeature;
use App\Models\FeatureLimit;
use App\Models\User;
use App\Models\UpgradePopup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonetizationService
{
    /**
     * Verifica se o usuário pode acessar uma funcionalidade baseado em seu plano e limites.
     */
    public function checkAccess(User $user, string $featureCode): array
    {
        $feature = AppFeature::where('code', $featureCode)->where('is_active', true)->first();

        if (!$feature) {
            return ['allowed' => false, 'reason' => 'Feature not found or inactive'];
        }

        // Se for Free categoria e não tiver limites específicos, libera
        if ($feature->category === 'free') {
            return ['allowed' => true];
        }

        $planId = $user->activePlan?->id; // Assume que User tem uma relação ou método activePlan
        
        // Buscar limite para o plano do usuário (ou null se for free/sem plano)
        $limit = FeatureLimit::where('feature_id', $feature->id)
            ->where('plan_id', $planId)
            ->first();

        // Se não houver limite configurado para o plano, mas a categoria for Premium e o usuário não for Premium
        if (!$limit && $feature->category === 'premium' && !$user->isPremium()) {
            return [
                'allowed' => false,
                'action' => 'popup',
                'feature' => $feature,
                'popup' => $this->getPopupData($featureCode)
            ];
        }

        // Se não houver limite configurado, assume acesso total para quem tem o plano correto
        if (!$limit) {
            return ['allowed' => true];
        }

        // Verificar o uso atual
        $usageCount = $this->getCurrentUsage($user, $feature, $limit->limit_type);

        if ($limit->limit_type !== 'none' && $usageCount >= $limit->limit_value) {
            return [
                'allowed' => false,
                'action' => $limit->action_type,
                'message' => $limit->custom_popup_text,
                'feature' => $feature,
                'popup' => $this->getPopupData($featureCode)
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Registra o uso de uma funcionalidade.
     */
    public function logUsage(User $user, string $featureCode)
    {
        $feature = AppFeature::where('code', $featureCode)->first();
        if (!$feature) return;

        DB::table('feature_usage_logs')->insert([
            'user_id' => $user->id,
            'feature_id' => $feature->id,
            'used_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getCurrentUsage(User $user, AppFeature $feature, string $type): int
    {
        $query = DB::table('feature_usage_logs')
            ->where('user_id', $user->id)
            ->where('feature_id', $feature->id);

        switch ($type) {
            case 'day':
                $query->whereDate('used_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('used_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('used_at', Carbon::now()->month)
                      ->whereYear('used_at', Carbon::now()->year);
                break;
            case 'lifetime':
                break;
            default:
                return 0;
        }

        return $query->count();
    }

    private function getPopupData(string $featureCode)
    {
        return UpgradePopup::where('feature_code', $featureCode)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Lógica de Limites de Recursos (SaaS)
    |--------------------------------------------------------------------------
    */

    public function hasFeature(User $user, string $featureKey): bool
    {
        if ($user->isAdministrator() || $user->hasPremiumAccess()) {
            return true;
        }

        $activePlan = $user->activePlan;
        $planId = $activePlan ? $activePlan->plan_id : 'free';
        
        $features = \Cache::remember("plan_features_v2_{$planId}", 3600, function() use ($activePlan) {
            $plan = $activePlan ? $activePlan->plan : \App\Models\Plan::where('name', 'Free')->first();
            if (!$plan) return [];
            
            return $plan->planFeatures()
                ->where('is_enabled', true)
                ->pluck('feature_key')
                ->toArray();
        });

        return in_array($featureKey, $features);
    }

    public function getPlanLimit(User $user, string $limitKey): int
    {
        if ($user->isAdministrator() || $user->hasPremiumAccess()) {
            return 0; // Ilimitado
        }

        $activePlan = $user->activePlan;
        $planId = $activePlan ? $activePlan->plan_id : 'free';
        
        $limits = \Cache::remember("plan_limits_v2_{$planId}", 3600, function() use ($activePlan) {
            $plan = $activePlan ? $activePlan->plan : \App\Models\Plan::where('name', 'Free')->first();
            return $plan ? $plan->toArray() : [];
        });

        return $limits[$limitKey] ?? 0;
    }

    public function isOverLimit(User $user, string $resourceType): bool
    {
        $limitKey = $this->getResourceLimitKey($resourceType);
        $limit = $this->getPlanLimit($user, $limitKey);
        
        if ($limit === 0) return false;

        $count = $this->getResourceCount($user, $resourceType);
        
        return $count > $limit;
    }

    public function getSurplusCount(User $user, string $resourceType): int
    {
        $limitKey = $this->getResourceLimitKey($resourceType);
        $limit = $this->getPlanLimit($user, $limitKey);
        
        if ($limit === 0) return 0;

        $count = $this->getResourceCount($user, $resourceType);
        
        return max(0, $count - $limit);
    }

    public function isResourceOverLimit(User $user, string $resourceType, $resourceId): bool
    {
        $limitKey = $this->getResourceLimitKey($resourceType);
        $limit = $this->getPlanLimit($user, $limitKey);
        
        if ($limit === 0) return false;

        $resourceIds = $this->getActiveResourceIds($user, $resourceType, $limit);
        
        return !in_array($resourceId, $resourceIds);
    }

    private function getResourceLimitKey(string $resourceType): string
    {
        return match($resourceType) {
            'patients', 'students' => 'max_patients',
            'workouts', 'training_plans' => 'max_workouts',
            'diets', 'nutrition_plans' => 'max_diets',
            'assessments' => 'max_assessments',
            default => 'max_' . $resourceType
        };
    }

    private function getResourceCount(User $user, string $resourceType): int
    {
        return match($resourceType) {
            'patients' => $user->patients()->count(),
            'workouts', 'training_plans' => $user->trainingPlans()->count(),
            'diets' => 0, 
            'assessments' => $user->assessments()->count(),
            default => 0
        };
    }

    private function getActiveResourceIds(User $user, string $resourceType, int $limit): array
    {
        return match($resourceType) {
            'patients' => $user->patients()->orderBy('pacientes.created_at', 'asc')->limit($limit)->pluck('users.id')->toArray(),
            'workouts', 'training_plans' => $user->trainingPlans()->orderBy('created_at', 'asc')->limit($limit)->pluck('id')->toArray(),
            'assessments' => $user->assessments()->orderBy('created_at', 'asc')->limit($limit)->pluck('id')->toArray(),
            default => []
        };
    }
}
