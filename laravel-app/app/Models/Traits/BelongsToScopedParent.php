<?php

namespace App\Models\Traits;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Isola registos filhos pela empresa do modelo pai (ex.: comment → post.academy_company_id).
 */
trait BelongsToScopedParent
{
    protected static function scopedParentRelationName(): string
    {
        return 'post';
    }

    protected static function scopedParentTenantColumnName(): string
    {
        return 'academy_company_id';
    }

    public static function bootBelongsToScopedParent(): void
    {
        static::addGlobalScope('parent_tenant_scope', function (Builder $builder) {
            \App\Support\TenantGuard::protect(function () use ($builder) {
                if (! Auth::hasUser()) {
                    return;
                }

                $user = Auth::user();

                if ($user->is_admin && ! session()->has('impersonated_clinic_id')) {
                    return;
                }

                $companyId = TenantContext::getCompanyId();
                if (! $companyId) {
                    return;
                }

                $relation = static::scopedParentRelationName();
                $column = static::scopedParentTenantColumnName();

                $builder->whereHas($relation, function (Builder $q) use ($column, $companyId) {
                    $q->where($column, $companyId);
                });
            });
        });
    }
}
