<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthPermission extends Model
{
    use Traits\BelongsToScopedParent;

    protected static function scopedParentRelationName(): string
    {
        return 'patient';
    }

    protected $table = 'health_permissions';

    protected $fillable = [
        'patient_id',
        'professional_id',
        'data_type',
        'access_level',
        'is_confidential',
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the professional.
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
