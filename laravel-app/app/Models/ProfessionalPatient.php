<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalPatient extends Model
{
    use Traits\BelongsToCompany;

    /** @var string */
    protected $companyColumn = 'empresa_id';

    protected $table = 'pacientes';

    protected $fillable = [
        'profissional_id',
        'user_id',
        'patient_permissions',
        'linked_by',
        'linking_ip',
        'linking_device',
        'data_cadastro',
        'status',
        'empresa_id',
        'tracking_status',
        'professional_notes_for_patient',
        'data_fim',
        'motivo_desvinculacao',
        'is_favorite',
        'last_accessed_at'
    ];

    protected $casts = [
        'patient_permissions' => 'array',
        'is_favorite' => 'boolean',
        'last_accessed_at' => 'datetime'
    ];

    public function professional()
    {
        return $this->belongsTo(User::class, 'profissional_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'linked_by');
    }

    /**
     * Retorna se o paciente tem permissão para uma funcionalidade específica.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->patient_permissions[$permission] ?? true; // Default true se não configurado? Ou false?
        // Vou assumir true por agora, mas a UI deve permitir desligar.
    }
}
