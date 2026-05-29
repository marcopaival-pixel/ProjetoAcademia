<?php

namespace App\Models\Traits;

use App\Models\Clinic;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasClinic
{
    /**
     * Boot the trait and apply the global scope for Clinic isolation.
     */
    public static function bootHasClinic()
    {
        static::addGlobalScope('clinic_isolation', function (Builder $builder) {
            if (!Auth::hasUser()) {
                return;
            }

            $user = Auth::user();

            // Admins see everything unless impersonating
            if ($user->is_admin && !session()->has('impersonated_clinic_id')) {
                return;
            }

            $clinicId = TenantContext::get();

            if ($clinicId) {
                $model = $builder->getModel();
                $tableName = $model->getTable();
                $column = property_exists($model, 'clinicColumn') ? $model->clinicColumn : 'clinic_id';

                $builder->where($tableName . '.' . $column, $clinicId);
            }
        });

        static::creating(function ($model) {
            $clinicId = TenantContext::get();
            if ($clinicId) {
                $column = property_exists($model, 'clinicColumn') ? $model->clinicColumn : 'clinic_id';
                if (!$model->{$column}) {
                    $model->{$column} = $clinicId;
                }
            }
        });
    }

    /**
     * Relacionamento com a Clínica.
     */
    public function clinic()
    {
        $column = property_exists($this, 'clinicColumn') ? $this->clinicColumn : 'clinic_id';
        return $this->belongsTo(Clinic::class, $column);
    }
}
