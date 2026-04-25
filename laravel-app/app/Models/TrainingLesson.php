<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingLesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'video_url',
        'content',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(TrainingModule::class, 'module_id');
    }
}
