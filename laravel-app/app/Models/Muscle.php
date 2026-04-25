<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Muscle extends Model
{
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
}
