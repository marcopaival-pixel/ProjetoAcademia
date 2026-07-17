<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalCertificate extends Model
{
    use Traits\HasClinic;

    protected $fillable = [
        'patient_id',
        'professional_id',
        'date',
        'reason',
        'start_date',
        'end_date',
        'period',
        'observations',
        'pdf_path',
    ];

    protected $casts = [
        'date' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
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
