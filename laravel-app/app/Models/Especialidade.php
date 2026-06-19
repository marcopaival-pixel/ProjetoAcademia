<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidade extends Model
{
    use HasFactory;

    protected $table = 'especialidades';

    protected $fillable = [
        'profession_id',
        'codigo',
        'nome',
        'categoria',
        'icone',
        'status',
        'client_term',
        'enabled_modules',
    ];

    protected $casts = [
        'enabled_modules' => 'array',
    ];

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Scope a query to only include active specialties.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Ativo');
    }

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'clinic_especialidade');
    }

    public function professionals()
    {
        return $this->belongsToMany(ProfessionalProfile::class, 'professional_profile_especialidade');
    }
}
