<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Isola registos pelo professional_id do utilizador autenticado.
 */
trait FiltersByProfessionalOwner
{
    public static function bootFiltersByProfessionalOwner(): void
    {
        static::addGlobalScope('professional_owner', function (Builder $builder) {
            if (! Auth::hasUser()) {
                return;
            }

            $user = Auth::user();

            if ($user->is_admin && ! session()->has('impersonated_clinic_id')) {
                return;
            }

            if (! $user->hasRole('professional')) {
                return;
            }

            $model = new static;
            $table = $model->getTable();

            if (in_array('professional_id', $model->getFillable(), true)) {
                $builder->where($table.'.professional_id', $user->id);
            }
        });
    }
}
