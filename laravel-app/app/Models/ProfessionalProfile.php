<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProfessionalProfile extends Model
{
    use Traits\HasClinic;

    protected $fillable = [
        'user_id',
        'profession_id',
        'especialidade_id',
        'specialty',
        'about',
        'service_types',
        'appointment_duration',
        'appointment_interval',
        'company_name',
        'registration_number',
        'council',
        'registration_uf',
        'registration_expiry_date',
        'document_path',
        'signature_path',
        'created_by',
        'updated_by',
        'document_version',
        'experience_years',
        'education',
        'professional_photo_path',
        'offered_services',
        'consultation_price',
        'clinic_address',
        'clinic_city',
        'clinic_state',
        'work_days',
        'work_start_time',
        'work_end_time',
        'is_public',
        'use_finance_module',
    ];

    protected $casts = [
        'registration_expiry_date' => 'date',
        'service_types' => 'array',
        'work_days' => 'array',
        'is_public' => 'boolean',
        'use_finance_module' => 'boolean',
        'consultation_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function especialidade(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class);
    }

    public function especialidades(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Especialidade::class, 'professional_profile_especialidade');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Verifica se o registro profissional está próximo do vencimento.
     * Retorna os dias restantes ou null.
     */
    public function daysUntilExpiry(): ?int
    {
        if (!$this->registration_expiry_date) return null;
        
        $expiry = Carbon::parse($this->registration_expiry_date);
        $diff = now()->diffInDays($expiry, false);
        return (int) $diff;
    }

    public function getExpiryWarningAttribute(): ?string
    {
        $days = $this->daysUntilExpiry();
        
        if ($days <= 0) return "Registro profissional VENCIDO";
        if ($days <= 7) return "Registro profissional próximo do vencimento (7 dias)";
        if ($days <= 15) return "Registro profissional próximo do vencimento (15 dias)";
        if ($days <= 30) return "Registro profissional próximo do vencimento (30 dias)";

        return null;
    }
}
