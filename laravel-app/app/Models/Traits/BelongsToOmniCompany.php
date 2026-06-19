<?php

namespace App\Models\Traits;

use App\Models\AcademyCompany;
use App\Models\OmniCompany;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Isolamento por omni_companies.id (coluna company_id).
 * Mapeia empresa ativa (AcademyCompany) via slug quando aplicável.
 */
trait BelongsToOmniCompany
{
    public static function bootBelongsToOmniCompany(): void
    {
        static::addGlobalScope('omni_company_isolation', function (Builder $builder) {
            \App\Support\TenantGuard::protect(function () use ($builder) {
                if (! Auth::hasUser()) {
                    return;
                }

                $user = Auth::user();

                if ($user->is_admin && ! session()->has('impersonated_clinic_id')) {
                    return;
                }

                $omniIds = self::resolveOmniCompanyIds();

                if ($omniIds === null) {
                    return;
                }

                if ($omniIds === []) {
                    $builder->whereRaw('1 = 0');

                    return;
                }

                $table = $builder->getModel()->getTable();
                $column = property_exists($builder->getModel(), 'omniCompanyColumn')
                    ? $builder->getModel()->omniCompanyColumn
                    : 'company_id';

                $builder->whereIn($table . '.' . $column, $omniIds);
            });
        });

        static::creating(function ($model) {
            $column = property_exists($model, 'omniCompanyColumn') ? $model->omniCompanyColumn : 'company_id';

            if (! empty($model->{$column})) {
                return;
            }

            $ids = self::resolveOmniCompanyIds();
            if ($ids !== null && count($ids) === 1) {
                $model->{$column} = $ids[0];
            }
        });
    }

    /**
     * @return list<int>|null null = sem filtro (admin global)
     */
    protected static function resolveOmniCompanyIds(): ?array
    {
        if (session()->has('active_omni_company_id')) {
            return [(int) session('active_omni_company_id')];
        }

        $companyId = TenantContext::getCompanyId();
        if (! $companyId) {
            return null;
        }

        $slug = AcademyCompany::query()->whereKey($companyId)->value('slug');
        if (! $slug) {
            return [];
        }

        return OmniCompany::query()->where('slug', $slug)->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}
