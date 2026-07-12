<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSession extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'training_plan_id',
        'session_date',
        'status',
        'started_at',
        'ended_at',
        'completion_percent',
        'completed_exercise_ids',
        'rpe_score',
        'mood',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'completed_exercise_ids' => 'array',
            'completion_percent' => 'integer',
            'rpe_score' => 'integer',
        ];
    }
}
