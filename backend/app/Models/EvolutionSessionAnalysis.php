<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvolutionSessionAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'session_date',
        'photo_hash',
        'analysis',
        'model_name',
        'total_tokens',
        'cost_usd',
    ];

    protected $casts = [
        'session_date' => 'date',
        'analysis' => 'array',
        'total_tokens' => 'integer',
        'cost_usd' => 'decimal:6',
    ];
}
