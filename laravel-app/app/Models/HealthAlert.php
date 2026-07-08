<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthAlert extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'type',
        'severity',
        'message',
        'is_read',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
