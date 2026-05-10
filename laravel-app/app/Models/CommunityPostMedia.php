<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityPostMedia extends Model
{
    protected $table = 'community_post_media';

    protected $fillable = [
        'post_id',
        'file_path',
        'type',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->type === 'sticker') {
            return asset($this->file_path);
        }
        return asset('storage/' . $this->file_path);
    }
}
