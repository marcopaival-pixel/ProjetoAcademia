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
    ];

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function professionalProfiles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ProfessionalProfile::class, 'especialidade_professional_profile')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active specialties.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Ativo');
    }
}
