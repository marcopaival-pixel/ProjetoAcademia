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

    public function completions()
    {
        return $this->hasMany(TrainingLessonCompletion::class, 'lesson_id');
    }

    public function isCompletedBy(User $user): bool
    {
        return $this->completions()->where('user_id', $user->id)->exists();
    }
}
