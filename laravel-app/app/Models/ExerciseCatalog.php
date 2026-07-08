<?php

namespace App\Models;

use App\Traits\Configurable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'tips',
        'common_mistakes',
        'video_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tips' => 'array',
        'common_mistakes' => 'array',
    ];

    public function muscles(): BelongsToMany
    {
        return $this->belongsToMany(Muscle::class, 'exercise_muscles', 'exercise_id', 'muscle_id');
    }
}
