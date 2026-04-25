<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplement extends Model
{
    protected $fillable = [
        'user_id',
        'smart_stack_id',
        'name',
        'dosage',
        'unit',
        'frequency',
        'time_of_day',
        'duration_days',
        'supplement_goal',
        'observations',
        'last_taken_at',
        'is_active',
    ];

    protected $casts = [
        'last_taken_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function smartStack(): BelongsTo
    {
        return $this->belongsTo(SmartStack::class);
    }
}
