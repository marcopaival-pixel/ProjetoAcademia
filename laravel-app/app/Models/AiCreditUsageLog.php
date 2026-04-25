<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCreditUsageLog extends Model
{
    use HasFactory;

    protected $table = 'ai_credits_usage_logs';

    protected $fillable = [
        'user_id',
        'action_type',
        'credits_consumed',
        'metadata',
        'response_cache_key',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
