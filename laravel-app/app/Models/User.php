<?php

namespace App\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Traits\HasPremiumAccess, Traits\HasOnboarding, Traits\HasProfessionalRelations, Traits\FiltersByProfessional, Traits\BelongsToCompany;

    protected static function booted()
    {
        static::saving(function ($user) {
            if ($user->cpf) {
                $user->cpf = \App\Support\Cpf::normalize($user->cpf);
            }
        });

        static::created(function ($user) {
            $planId = $user->plan_id;
            
            if (!$planId) {
                $freePlan = \App\Models\Plan::where('name', 'Free')->first();
                $planId = $freePlan?->id;
            }

            if ($planId) {
                $user->userPlans()->create([
                    'plan_id' => $planId,
                    'start_date' => now(),
                    'status' => 'active',
                ]);
            }
        });
    }

    const CREATED_AT = 'created_at';

    // UPDATED_AT mantido ativo (padrão Laravel) para auditabilidade de alterações do usuário.
    // Não anular — alterações de password, role, premium_status e status ficam rastreáveis.

    protected $fillable = [
        'name',
        'username',
        'email',
        'cpf',
        'email_verified_at',
        'email_verification_token',
        'email_verification_expires_at',
        'data_envio_confirmacao',
        'tentativas_envio',
        // password_hash removido do fillable — atribuir explicitamente: $user->password_hash = Hash::make($plain)
        'is_premium',
        'is_admin',
        'onboarding_status',
        'profile_completion_percentage',
        'premium_expires_at',
        'department',
        'phone',
        'profile_id', // Mantido temporariamente para compatibilidade legada se necessário
        'plan_id',
        'status',
        'professional_code',
        'qr_code_path',
        'professional_plan_id',
        'is_demo',
        'demo_expires_at',
        'last_activity_at',
        'health_score',
        'churn_risk',
        'usage_stats',
        'academy_company_id',
        'registration_approval_status',
        'registration_reviewed_at',
        'registration_rejection_note',
        'google_id',
        'provider',
        'avatar',
        'perfil_paciente_completo',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'is_admin' => 'boolean',
            'profile_completion_percentage' => 'integer',
            'premium_expires_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'email_verification_expires_at' => 'datetime',
            'data_envio_confirmacao' => 'datetime',
            'tentativas_envio' => 'integer',
            'created_at' => 'datetime',
            'demo_expires_at' => 'datetime',
            'is_demo' => 'boolean',
            'last_activity_at' => 'datetime',
            'health_score' => 'integer',
            'usage_stats' => 'array',
            'registration_reviewed_at' => 'datetime',
            'perfil_paciente_completo' => 'boolean',
        ];
    }

    public function isRegistrationPending(): bool
    {
        return $this->registration_approval_status === 'pending';
    }

    public function isRegistrationRejected(): bool
    {
        return $this->registration_approval_status === 'rejected';
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos de Perfil e RBAC
    |--------------------------------------------------------------------------
    */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')->withTimestamps();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function professionalProfile(): HasOne
    {
        return $this->hasOne(ProfessionalProfile::class, 'user_id', 'id');
    }

    public function userProfile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // Fallback para código que ainda usa profile_id
        return $this->belongsTo(Role::class, 'profile_id');
    }

    public function userRole(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'profile_id');
    }

    public function plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function academyCompany(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }

    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return $this->roles()->whereIn('name', $role)->exists();
        }
        return $this->roles()->where('name', $role)->exists();
    }

    public function assignRole(string $roleName): void
    {
        // Regras de Combinação de Perfis
        if ($roleName === 'paciente' && $this->hasRole('professional')) {
            throw new \Exception('Um profissional não pode ser cadastrado como paciente.');
        }

        if ($roleName === 'professional' && $this->hasRole('paciente')) {
            throw new \Exception('Um paciente não pode ser promovido a profissional (remova o perfil de paciente primeiro).');
        }

        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    public function removeRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    public function hasPermission(string|array $permission): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if (is_array($permission)) {
            foreach ($permission as $p) {
                if ($this->hasPermission($p)) {
                    return true;
                }
            }
            return false;
        }

        return $this->roles()->whereHas('permissions', function($q) use ($permission) {
            $q->where('name', $permission);
        })->exists();
    }

    public function hasPlanPermission(string $permission): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        return $this->plan?->permissions()->where('name', $permission)->exists() ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Status e Administração
    |--------------------------------------------------------------------------
    */

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isAdministrator(): bool
    {
        return (bool) $this->is_admin;
    }

    public function hasAdminPanelAccess(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return $this->hasPermission('admin.access');
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos Funcionais (Logs, Mensagens, Treinos)
    |--------------------------------------------------------------------------
    */

    public function mealTemplates(): HasMany
    {
        return $this->hasMany(MealTemplate::class, 'user_id', 'id');
    }

    public function waterEntries(): HasMany
    {
        return $this->hasMany(WaterEntry::class, 'user_id', 'id');
    }

    public function weightEntries(): HasMany
    {
        return $this->hasMany(WeightEntry::class, 'user_id', 'id');
    }

    public function foodEntries(): HasMany
    {
        return $this->hasMany(FoodEntry::class, 'user_id', 'id');
    }

    public function exerciseEntries(): HasMany
    {
        return $this->hasMany(ExerciseEntry::class, 'user_id', 'id');
    }

    public function aiChats(): HasMany
    {
        return $this->hasMany(AIChat::class, 'user_id', 'id');
    }

    public function loadLogs(): HasMany
    {
        return $this->hasMany(LoadLog::class, 'user_id', 'id');
    }

    public function trainingPlans(): HasMany
    {
        return $this->hasMany(TrainingPlan::class, 'user_id');
    }

    public function workoutSessions(): HasMany
    {
        return $this->hasMany(WorkoutSession::class, 'user_id');
    }

    public function evolutionPhotos(): HasMany
    {
        return $this->hasMany(EvolutionPhoto::class, 'user_id');
    }

    /**
     * Usuários que este usuário bloqueou.
     */
    public function blockedUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocker_id', 'blocked_id')->withTimestamps();
    }

    /**
     * Usuários que bloquearam este usuário.
     */
    public function blockers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_id', 'blocker_id')->withTimestamps();
    }

    public function isBlocking(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }

    public function isBlockedBy(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->blockers()->where('blocker_id', $userId)->exists();
    }

    /**
     * Envia a notificação de redefinição de senha.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordCustom($token));
    }

    /**
     * Grupos de comunicação que o usuário participa.
     */
    public function communicationGroups(): BelongsToMany
    {
        return $this->belongsToMany(CommunicationGroup::class, 'communication_group_user', 'user_id', 'group_id')
            ->withPivot('status', 'role')
            ->withTimestamps();
    }

    public function branding(): HasOne
    {
        return $this->hasOne(ProfessionalBranding::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(BodyAssessment::class, 'user_id');
    }

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pacientes', 'profissional_id', 'user_id')
            ->withPivot('data_cadastro', 'status', 'empresa_id')
            ->withTimestamps();
    }

    public function professionals(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pacientes', 'user_id', 'profissional_id')
            ->withPivot('data_cadastro', 'status', 'empresa_id')
            ->withTimestamps();
    }

    public function receivedRequests(): HasMany
    {
        return $this->hasMany(ProfessionalPatientRequest::class, 'professional_id');
    }

    public function sentRequests(): HasMany
    {
        return $this->hasMany(ProfessionalPatientRequest::class, 'patient_id');
    }

    public function professionalPlan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProfessionalPlan::class, 'professional_plan_id');
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(UserMenuPreference::class);
    }

    public function requestedCoupons(): HasMany
    {
        return $this->hasMany(Coupon::class, 'professional_id');
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->latest('id');
    }

    /**
     * Tokens de acesso seguro para o Portal do Paciente.
     */
    public function accessTokens(): HasMany
    {
        return $this->hasMany(PatientAccessToken::class, 'patient_id');
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(PatientTreatmentPlan::class, 'patient_id');
    }

    public function patientDocuments(): HasMany
    {
        return $this->hasMany(PatientDocument::class, 'patient_id');
    }

    public function medicalEvolutions(): HasMany
    {
        return $this->hasMany(MedicalEvolution::class, 'patient_id');
    }

    public function medicalReports(): HasMany
    {
        return $this->hasMany(MedicalReport::class, 'patient_id');
    }

    public function medicalPrescriptions(): HasMany
    {
        return $this->hasMany(MedicalPrescription::class, 'patient_id');
    }

    public function medicalCertificates(): HasMany
    {
        return $this->hasMany(MedicalCertificate::class, 'patient_id');
    }

    public function medicalHistories(): HasMany
    {
        return $this->hasMany(MedicalHistory::class, 'patient_id');
    }

    public function professionalMedicalEvolutions(): HasMany
    {
        return $this->hasMany(MedicalEvolution::class, 'professional_id');
    }

    public function professionalMedicalReports(): HasMany
    {
        return $this->hasMany(MedicalReport::class, 'professional_id');
    }

    public function professionalMedicalPrescriptions(): HasMany
    {
        return $this->hasMany(MedicalPrescription::class, 'professional_id');
    }

    public function professionalMedicalCertificates(): HasMany
    {
        return $this->hasMany(MedicalCertificate::class, 'professional_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(ProfessionalAvailability::class, 'professional_id');
    }

    public function activePlan()
    {
        return $this->hasOne(UserPlan::class)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->latest();
    }

    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }

    public function hasFeature(string $featureKey): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        $activePlan = $this->activePlan;
        
        if (!$activePlan) {
            // Fallback to FREE plan features if no active plan record exists
            return \Cache::remember("plan_feature_free_{$featureKey}", 3600, function() use ($featureKey) {
                $freePlan = Plan::where('name', 'Free')->first();
                return $freePlan ? $freePlan->hasFeature($featureKey) : false;
            });
        }

        return $activePlan->plan->hasFeature($featureKey);
    }

    public function getPlanLimit(string $limitKey): int
    {
        $activePlan = $this->activePlan;
        
        if (!$activePlan) {
            // Fallback to FREE plan limits
            return \Cache::remember("plan_limit_free_{$limitKey}", 3600, function() use ($limitKey) {
                $freePlan = Plan::where('name', 'Free')->first();
                return $freePlan ? ($freePlan->{$limitKey} ?? 0) : 0;
            });
        }

        return $activePlan->plan->{$limitKey} ?? 0;
    }

    public function aiUsage(): HasMany
    {
        return $this->hasMany(AiCreditUsageLog::class);
    }

    public function getAiCreditsUsedThisMonth(): int
    {
        return $this->aiUsage()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('credits_consumed');
    }

    public function getRemainingAiCredits(): int
    {
        return (int) $this->ai_credits;
    }

    public function consumeAiCredit(string $actionType, array $metadata = []): bool
    {
        return app(\App\Services\AiCreditService::class)->consume($this, $actionType, $metadata);
    }
}
