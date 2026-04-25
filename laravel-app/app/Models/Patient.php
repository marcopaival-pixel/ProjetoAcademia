<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use Traits\BelongsToCompany;

    protected $table = 'pacientes';
    protected $companyColumn = 'empresa_id';

    protected $fillable = [
        'user_id',
        'profissional_id',
        'data_cadastro',
        'status',
        'empresa_id'
    ];

    protected $casts = [
        'data_cadastro' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profissional_id');
    }

    public function academyCompany(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'empresa_id');
    }

    public function evolutions()
    {
        return $this->hasMany(MedicalEvolution::class, 'patient_id', 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(MedicalReport::class, 'patient_id', 'user_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(MedicalPrescription::class, 'patient_id', 'user_id');
    }

    public function certificates()
    {
        return $this->hasMany(MedicalCertificate::class, 'patient_id', 'user_id');
    }

    public function histories()
    {
        return $this->hasMany(MedicalHistory::class, 'patient_id', 'user_id');
    }
}
