<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_date',
        'rpe_score',
        'mood',
        'notes'
    ];
}
