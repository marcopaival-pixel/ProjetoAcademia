<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Configurable;

class Muscle extends Model
{
    use Configurable;
    protected $fillable = ['group_id', 'name', 'type', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(MuscleGroup::class, 'group_id');
    }

    public function exercises()
    {
        return $this->belongsToMany(ExerciseCatalog::class, 'exercise_muscles', 'muscle_id', 'exercise_id');
    }

    protected static function booted()
    {
        static::deleting(function ($muscle) {
            if (!auth()->check() || !auth()->user()->isAdministrator()) {
                abort(403, 'Apenas administradores podem excluir músculos do sistema. Esta tabela é protegida.');
            }
        });
    }
}
