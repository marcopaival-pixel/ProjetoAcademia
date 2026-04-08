<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseEntry extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'entry_date',
        'activity_type',
        'duration_min',
        'calories_burned',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'duration_min' => 'integer',
        'calories_burned' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
