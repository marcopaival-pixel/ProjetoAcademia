<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsage extends Model
{
    protected $table = 'ai_usage';

    protected $fillable = [
        'user_id',
        'feature',
        'credits_used'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
