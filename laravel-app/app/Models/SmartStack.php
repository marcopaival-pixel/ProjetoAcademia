<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmartStack extends Model
{
    protected $fillable = [
        'user_id',
        'professional_id',
        'name',
        'goal',
        'target_audience',
        'responsible_type',
        'status',
        'start_date',
        'end_date',
        'adherence_rate',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'adherence_rate' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function supplements(): HasMany
    {
        return $this->hasMany(Supplement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ativo');
    }
}
