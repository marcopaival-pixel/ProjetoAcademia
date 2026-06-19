<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientErrorLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'stack',
        'url',
        'user_agent',
        'ip',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
