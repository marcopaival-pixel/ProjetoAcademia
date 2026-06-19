<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiAccessLog extends Model
{
    protected $fillable = [
        'request_id',
        'user_id',
        'token_id',
        'method',
        'path',
        'status_code',
        'duration_ms',
        'ip',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
