<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSession extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'session_date',
        'rpe_score',
        'mood',
        'notes'
    ];
}
