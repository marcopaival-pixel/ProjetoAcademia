<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model
{
    protected $fillable = [
        'academy_company_id',
        'name',
        'slug',
        'logo_path',
        'primary_color',
        'custom_domain',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com a conta principal (empresa/faturamento).
     */
    public function academyCompany(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class);
    }

    /**
     * Usuários vinculados a esta clínica.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Pacientes vinculados a esta clínica.
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Planos de treino da clínica.
     */
    public function trainingPlans(): HasMany
    {
        return $this->hasMany(TrainingPlan::class);
    }

    /**
     * Avaliações físicas da clínica.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(BodyAssessment::class);
    }

    /**
     * Retorna a URL do logo ou um fallback.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return asset('images/default-clinic-logo.png');
    }
}
