<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Isola registos do canal comercial pelo representante autenticado.
 */
trait FiltersByRepresentative
{
    public static function bootFiltersByRepresentative(): void
    {
        static::addGlobalScope('representative_access', function (Builder $builder) {
            if (! Auth::hasUser()) {
                return;
            }

            $user = Auth::user();

            if ($user->is_admin && ! session()->has('impersonated_clinic_id')) {
                return;
            }

            if (! $user->hasRole('representative')) {
                return;
            }

            $model = new static;
            $tableName = $model->getTable();
            $fillable = $model->getFillable();

            if (in_array('representative_id', $fillable, true)) {
                $builder->where($tableName.'.representative_id', $user->id);
            } elseif (in_array('user_id', $fillable, true)) {
                $builder->where($tableName.'.user_id', $user->id);
            }
        });
    }
}
