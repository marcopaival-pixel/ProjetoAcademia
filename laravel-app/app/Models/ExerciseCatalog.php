<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Configurable;

class ExerciseCatalog extends Model
{
    use Configurable;
    protected $table = 'exercises_catalog';

    protected $fillable = [
        'name',
        'muscle_group',
        'equipment',
        'difficulty',
        'instructions',
        'video_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function muscles()
    {
        return $this->belongsToMany(Muscle::class, 'exercise_muscles', 'exercise_id', 'muscle_id');
    }
}
