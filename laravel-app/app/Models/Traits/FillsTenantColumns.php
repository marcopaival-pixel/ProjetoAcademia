<?php

namespace App\Models\Traits;

use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;

/**
 * Preenche clinic_id (via HasClinic) e academy_company_id em registos operacionais.
 */
trait FillsTenantColumns
{
    public static function bootFillsTenantColumns(): void
    {
        static::creating(function ($model) {
            if (! Auth::hasUser()) {
                return;
            }

            $user = Auth::user();

            if (empty($model->academy_company_id)) {
                $model->academy_company_id = $user->academy_company_id
                    ?? TenantContext::getCompanyId();
            }

            if (property_exists($model, 'clinic_id') && empty($model->clinic_id)) {
                $model->clinic_id = TenantContext::get() ?? $user->clinic_id;
            }
        });
    }
}
