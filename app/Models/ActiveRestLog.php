<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveRestLog extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id', 
        'active_rest_routine_id', 
        'duration_spent', 
        'feedback_score', 
        'notes'
    ];
}
