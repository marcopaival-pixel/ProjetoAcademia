<?php

namespace App\Models\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait para isolamento automático por Organização (Tenant).
 * Aplica o Global Scope para todas as consultas do Model.
 */
trait BelongsToOrganization
{
    public static function bootBelongsToOrganization()
    {
        static::addGlobalScope('organization_isolation', function (Builder $builder) {
            // Se o contexto de organização estiver definido na sessão/app
            if (session()->has('active_organization_id')) {
                $orgId = session('active_organization_id');
                $model = $builder->getModel();
                $tableName = $model->getTable();
                
                // Filtra pela coluna organization_id
                $builder->where($tableName . '.organization_id', $orgId);
            }
        });

        // Define automaticamente o organization_id ao criar um novo registro
        static::creating(function ($model) {
            if (session()->has('active_organization_id') && empty($model->organization_id)) {
                $model->organization_id = session('active_organization_id');
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
