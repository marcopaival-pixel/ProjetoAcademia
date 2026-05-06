<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Patient extends Model
{
    // Removendo 'pacientes' legada se as migrações novas forem aplicadas
    protected $table = 'patients';

    protected $fillable = [
        'uuid',
        'name',
        'cpf',
        'email',
        'birth_date',
        'gender',
        'user_id'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_patient')
            ->withPivot('internal_code')
            ->withTimestamps();
    }

    /**
     * Registros clínicos vinculados a este paciente.
     * Importante: Devem sempre ser filtrados pelo organization_id ativo.
     */
    public function evolutions()
    {
        return $this->hasMany(MedicalEvolution::class, 'patient_id');
    }
}
