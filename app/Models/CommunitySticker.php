<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunitySticker extends Model
{
    protected $fillable = [
        'name',
        'path',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }
}
