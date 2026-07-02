<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PainRecord extends Model
{
    use HasFactory, Traits\FiltersByProfessional, Traits\HasClinic;

    protected $fillable = [
        'user_id',
        'professional_id',
        'pain_points',
        'eva_level',
        'notes',
        'assessment_date',
    ];

    protected $casts = [
        'pain_points' => 'array',
        'eva_level' => 'integer',
        'assessment_date' => 'datetime',
    ];

    /**
     * Get the patient user that owns the pain record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the professional user that recorded the pain record.
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
