<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActiveRestRoutine extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'duration', 'intensity', 'thumbnail', 'guide_image', 
        'video_id', 'benefit', 'is_premium', 'exercises', 
        'execution_steps', 'tips', 'common_errors', 'order', 'is_active'
    ];

    protected $casts = [
        'exercises' => 'array',
        'execution_steps' => 'array',
        'tips' => 'array',
        'common_errors' => 'array',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];
}
