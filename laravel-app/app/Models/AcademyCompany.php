<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyCompany extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'primary_color',
        'accent_color',
        'shared_medical_records',
        'legal_name',
        'tax_id',
        'responsible_name',
        'responsible_email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'pdf_settings',
        'is_active',
        'onboarding_status',
        'current_onboarding_step',
    ];

    protected function casts(): array
    {
        return [
            'pdf_settings' => 'array',
            'is_active' => 'boolean',
            'shared_medical_records' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()->where('status', 'active')->latest()->first();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function professionals(): HasMany
    {
        return $this->users()->whereHas('roles', fn($q) => $q->where('name', 'professional'));
    }

    /**
     * @return HasMany<AcademyUnit, AcademyCompany>
     */
    public function units(): HasMany
    {
        return $this->hasMany(AcademyUnit::class);
    }

    /**
     * @return HasMany<PdfTemplate, AcademyCompany>
     */
    public function pdfTemplates(): HasMany
    {
        return $this->hasMany(PdfTemplate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<ConfiguracaoEmail, AcademyCompany>
     */
    public function configuracaoEmail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ConfiguracaoEmail::class, 'empresa_id');
    }

    public function watermarkConfig(): array
    {
        $s = $this->pdf_settings ?? [];

        return is_array($s['watermark'] ?? null) ? $s['watermark'] : [];
    }

    public function onboardingSteps(): HasMany
    {
        return $this->hasMany(ClinicOnboardingStep::class);
    }

    public function isOnboardingCompleted(): bool
    {
        return $this->onboarding_status === 'completed';
    }
}
