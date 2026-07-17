<?php

namespace App\Models\Traits;

use App\Models\AcademyCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * Boot the trait and apply the global scope.
     */
    public static function bootBelongsToCompany()
    {
        static::addGlobalScope('company_isolation', function (Builder $builder) {
            \App\Support\TenantGuard::protect(function () use ($builder) {
                if (!Auth::hasUser()) {
                    return;
                }

                $user = Auth::user();

                // Administradores globais veem tudo (a menos que estejam acessando uma clínica específica via impersonação)
                if ($user->is_admin && !session()->has('impersonated_clinic_id')) {
                    return;
                }

                $clinicId = \App\Support\TenantContext::getCompanyId();

                // Se existe uma clínica no contexto, filtramos os dados por ela
                if ($clinicId) {
                    $model = $builder->getModel();
                    $tableName = $model->getTable();
                    
                    // Determina a coluna de empresa (padrão academy_company_id)
                    $column = property_exists($model, 'companyColumn') ? $model->companyColumn : 'academy_company_id';
                    
                    // Se o modelo explicitamente diz para NÃO filtrar por coluna direta (ex: usar user_id)
                    if ($column === 'user_id') {
                        $builder->whereHas('user', function ($q) use ($clinicId) {
                            $q->where('academy_company_id', $clinicId);
                        });
                    } else {
                        $builder->where($tableName . '.' . $column, $clinicId);
                    }
                }
            });
        });

        // Evento de criação para definir automaticamente a empresa
        static::creating(function ($model) {
            $column = property_exists($model, 'companyColumn') ? $model->companyColumn : 'academy_company_id';

            if ($column === 'user_id' || ! empty($model->{$column})) {
                return;
            }

            $companyId = \App\Support\TenantContext::getCompanyId()
                ?? Auth::user()?->academy_company_id;

            if ($companyId) {
                $model->{$column} = $companyId;
            }
        });
    }

    /**
     * Relacionamento com a Empresa (AcademyCompany).
     */
    public function academyCompany()
    {
        $column = property_exists($this, 'companyColumn') ? $this->companyColumn : 'academy_company_id';
        $fk = ($column === 'user_id') ? 'academy_company_id' : $column;
        
        return $this->belongsTo(\App\Models\AcademyCompany::class, $fk);
    }
}
