<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientModule extends Model
{
    use Traits\BelongsToScopedParent;

    protected static function scopedParentRelationName(): string
    {
        return 'patient';
    }

    protected $table = 'patient_modules';

    protected $fillable = [
        'patient_id',
        'module_key',
        'is_enabled',
        'auto_discovered',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'auto_discovered' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient that owns this module preference.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
