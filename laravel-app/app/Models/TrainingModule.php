<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingModule extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lessons()
    {
        return $this->hasMany(TrainingLesson::class, 'module_id')->orderBy('order');
    }

    public function getProgressForUser(?User $user): int
    {
        if (!$user) return 0;
        $total = $this->lessons()->where('is_active', true)->count();
        if ($total === 0) return 0;
        
        $completed = \App\Models\TrainingLessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $this->lessons()->where('is_active', true)->pluck('id'))
            ->count();
            
        return (int) round(($completed / $total) * 100);
    }
}
