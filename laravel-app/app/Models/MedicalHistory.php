<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    use Traits\BelongsToScopedParent;

    protected static function scopedParentRelationName(): string
    {
        return 'patient';
    }

    protected $fillable = [
        'patient_id',
        'user_id',
        'action_type',
        'module',
        'description',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Log a medical action.
     */
    public static function log($patientId, $actionType, $module, $description)
    {
        return self::create([
            'patient_id' => $patientId,
            'user_id' => auth()->id(),
            'action_type' => $actionType,
            'module' => $module,
            'description' => $description,
        ]);
    }
}
