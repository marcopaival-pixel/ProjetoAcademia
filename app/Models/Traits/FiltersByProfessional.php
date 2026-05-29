<?php

namespace App\Models\Traits;

use App\Models\AdminLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FiltersByProfessional
{
    /**
     * Boot the trait and apply the global scope.
     */
    public static function bootFiltersByProfessional()
    {
        static::addGlobalScope('professional_access', function (Builder $builder) {
            // Evita recursividade infinita: se o usuário ainda não estiver carregado 
            // (ex: durante o próprio processo de autenticação), não aplicamos o escopo.
            if (! Auth::hasUser()) {
                return;
            }

            $user = Auth::user();
            if (! $user) {
                return;
            }

            // Administradores, Gerentes e Supervisores têm acesso total
            $exemptRoles = ['admin', 'manager', 'supervisor'];
            if ($user->is_admin || $user->hasRole($exemptRoles)) {
                return;
            }

            // Profissionais veem apenas dados vinculados
            if ($user->hasRole('professional')) {
                $model = new static;
                $tableName = $model->getTable();
                $fillable = $model->getFillable();

                // Verificar se a clínica permite prontuário compartilhado
                $isShared = false;
                if ($user->academy_company_id) {
                    $isShared = \Illuminate\Support\Facades\Cache::remember("company_shared_records_{$user->academy_company_id}", 3600, function() use ($user) {
                        return (bool) \App\Models\AcademyCompany::where('id', $user->academy_company_id)
                            ->where('shared_medical_records', true)
                            ->exists();
                    });
                }

                if ($tableName === 'users') {
                    // Filtra pacientes vinculados ao profissional (ou o próprio profissional)
                    // Se for compartilhado, vê todos os pacientes da empresa
                    $builder->where(function ($query) use ($user, $isShared) {
                        $query->whereHas('professionals', function ($q) use ($user) {
                            $q->withoutGlobalScope('professional_access')->where('profissional_id', $user->id);
                        })->orWhere('users.id', $user->id);

                        if ($isShared && $user->academy_company_id) {
                            $query->orWhere(function($q) use ($user) {
                                $q->where('academy_company_id', $user->academy_company_id)
                                  ->whereHas('roles', fn($r) => $r->whereIn('name', ['aluno', 'paciente']));
                            });
                        }
                    });
                } elseif (in_array('professional_id', $fillable)) {
                    // Filtra diretamente pelo ID do profissional no modelo
                    $builder->where(function($q) use ($tableName, $user, $isShared) {
                        $q->where($tableName.'.professional_id', $user->id);
                        
                        if ($isShared && $user->academy_company_id) {
                            $q->orWhereHas('professional', function($p) use ($user) {
                                $p->where('academy_company_id', $user->academy_company_id);
                            });
                        }
                    });
                } elseif (in_array('patient_id', $fillable)) {
                    // Filtra por pacientes vinculados quando o modelo usa 'patient_id'
                    $builder->where(function($query) use ($user, $isShared) {
                        $query->whereHas('patient.professionals', function ($q) use ($user) {
                            $q->withoutGlobalScope('professional_access')->where('profissional_id', $user->id);
                        });

                        if ($isShared && $user->academy_company_id) {
                            $query->orWhereHas('patient', function($p) use ($user) {
                                $p->where('academy_company_id', $user->academy_company_id);
                            });
                        }
                    });
                } elseif (in_array('user_id', $fillable)) {
                    // Filtra por pacientes (users) vinculados ao profissional
                    $builder->where(function($query) use ($user, $isShared) {
                        $query->whereHas('user.professionals', function ($q) use ($user) {
                            $q->withoutGlobalScope('professional_access')->where('profissional_id', $user->id);
                        });

                        if ($isShared && $user->academy_company_id) {
                            $query->orWhereHas('user', function($u) use ($user) {
                                $u->where('academy_company_id', $user->academy_company_id);
                            });
                        }
                    });
                } elseif ($tableName === 'messages') {
                    // Filtra mensagens em conversas onde o profissional participa
                    $builder->whereHas('conversation', function ($q) use ($user) {
                        $q->withoutGlobalScope('professional_access')->where('user_one_id', $user->id)
                            ->orWhere('user_two_id', $user->id);
                    });
                } elseif ($tableName === 'omni_messages') {
                    // Filtra mensagens omnichannel ligadas à empresa do profissional ou designadas a ele
                    $builder->where('professional_id', $user->id)
                        ->orWhere('sender_id', $user->id);
                }
            }
        });
    }


    /**
     * Registra o acesso a um dado sensível (Auditoria LGPD).
     */
    public function logAccess(string $action = 'view')
    {
        $user = Auth::user();
        if (!$user) return;

        AdminLog::create([
            'user_id' => $user->id,
            'action' => "ACCESS_{$action}_" . strtoupper(class_basename($this)),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => [
                'model_id' => $this->id,
                'model_class' => get_class($this),
                'timestamp' => now()->toDateTimeString(),
            ],
            'created_at' => now(),
        ]);
    }
}
