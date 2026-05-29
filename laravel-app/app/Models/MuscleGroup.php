<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuscleGroup extends Model
{
    protected $fillable = ['name', 'region', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function muscles()
    {
        return $this->hasMany(Muscle::class, 'group_id');
    }

    protected static function booted()
    {
        static::deleting(function ($group) {
            if (!auth()->check() || !auth()->user()->isAdministrator()) {
                abort(403, 'Apenas administradores podem excluir grupos musculares do sistema. Esta tabela é protegida.');
            }
        });
    }
}
