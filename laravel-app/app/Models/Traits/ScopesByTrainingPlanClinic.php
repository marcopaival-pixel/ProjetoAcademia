<?php

namespace App\Models\Traits;

use App\Support\TenantContext;
use App\Support\TenantGuard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Isola séries de exercício pela clínica do plano de treino pai.
 */
trait ScopesByTrainingPlanClinic
{
    public static function bootScopesByTrainingPlanClinic(): void
    {
        static::addGlobalScope('training_plan_clinic', function (Builder $builder) {
            TenantGuard::protect(function () use ($builder) {
                if (! Auth::hasUser()) {
                    return;
                }

                $user = Auth::user();
                if ($user->is_admin && ! session()->has('impersonated_clinic_id')) {
                    return;
                }

                $clinicId = TenantContext::get();
                if (! $clinicId) {
                    return;
                }

                $builder->whereHas('trainingPlanExercise.trainingPlan', function (Builder $q) use ($clinicId) {
                    $q->where('clinic_id', $clinicId);
                });
            });
        });
    }
}
