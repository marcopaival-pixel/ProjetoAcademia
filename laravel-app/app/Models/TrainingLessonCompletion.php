<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingLessonCompletion extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'lesson_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(TrainingLesson::class, 'lesson_id');
    }
}
