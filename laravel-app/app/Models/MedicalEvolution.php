<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalEvolution extends Model
{
    protected $fillable = [
        'patient_id',
        'professional_id',
        'date',
        'type',
        'chief_complaint',
        'assessment',
        'diagnosis',
        'conduct',
        'observations',
        'attachments',
    ];

    protected $casts = [
        'date' => 'datetime',
        'attachments' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
