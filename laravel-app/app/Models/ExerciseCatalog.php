<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseCatalog extends Model
{
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
}
