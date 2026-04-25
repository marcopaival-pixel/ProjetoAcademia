<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalPrescription extends Model
{
    protected $fillable = [
        'patient_id',
        'professional_id',
        'especialidade_id',
        'academy_company_id',
        'date',
        'objective',
        'protocol',
        'medicine',
        'dosage',
        'frequency',
        'duration',
        'observations',
        'pdf_path',
    ];

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class, 'especialidade_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }

    protected $casts = [
        'date' => 'datetime',
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
