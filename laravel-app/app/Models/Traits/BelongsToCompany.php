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
                if (!Auth::check()) {
                    return;
                }

                $user = Auth::user();

                // Administradores globais veem tudo (a menos que estejam acessando uma clínica específica via impersonação)
                if ($user->is_admin && !session()->has('impersonated_clinic_id')) {
                    return;
                }

                // Se o usuário logado pertence a uma empresa, filtramos os dados por ela
                if ($user->academy_company_id) {
                    $model = $builder->getModel();
                    $tableName = $model->getTable();
                    
                    // Determina a coluna de empresa (padrão academy_company_id)
                    $column = property_exists($model, 'companyColumn') ? $model->companyColumn : 'academy_company_id';
                    
                    // Se o modelo explicitamente diz para NÃO filtrar por coluna direta (ex: usar user_id)
                    if ($column === 'user_id') {
                        $builder->whereHas('user', function ($q) use ($user) {
                            $q->where('academy_company_id', $user->academy_company_id);
                        });
                    } else {
                        $builder->where($tableName . '.' . $column, $user->academy_company_id);
                    }
                }
            });
        });

        // Evento de criação para definir automaticamente a empresa
        static::creating(function ($model) {
            $user = Auth::user();
            if ($user && $user->academy_company_id) {
                $column = property_exists($model, 'companyColumn') ? $model->companyColumn : 'academy_company_id';
                
                if ($column !== 'user_id' && !$model->{$column}) {
                    $model->{$column} = $user->academy_company_id;
                }
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
