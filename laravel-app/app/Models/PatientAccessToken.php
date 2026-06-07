<?php

namespace App\Models;

use App\Models\Traits\BelongsToScopedParent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientAccessToken extends Model
{
    use BelongsToScopedParent;

    protected static function scopedParentRelationName(): string
    {
        return 'patient';
    }

    protected static function scopedParentTenantColumnName(): string
    {
        return 'academy_company_id';
    }
    protected $fillable = [
        'patient_id',
        'type',
        'token_hash',
        'expires_at',
        'used_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Relacionamento com o paciente (User).
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Verifica se o token é válido.
     */
    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->used_at) {
            return false;
        }

        return true;
    }
}
