<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'title',
        'type',
        'target_value',
        'current_value',
        'start_date',
        'end_date',
        'description',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'target_value' => 'float',
        'current_value' => 'float',
    ];

    /**
     * Get the progress percentage.
     */
    public function getProgressAttribute(): float
    {
        if ($this->target_value <= 0) return 0;
        $percentage = ($this->current_value / $this->target_value) * 100;
        return round($percentage, 2);
    }

    /**
     * Scope a query to only include active goals.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }
}
