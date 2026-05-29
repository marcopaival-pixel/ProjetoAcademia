<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPostQueue extends Model
{
    protected $table = 'social_post_queue';

    protected $fillable = [
        'post_id',
        'platform',
        'status',
        'scheduled_at',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class);
    }
}
