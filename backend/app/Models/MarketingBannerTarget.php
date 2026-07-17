<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingBannerTarget extends Model
{
    protected $fillable = [
        'banner_id',
        'role_id',
    ];

    public function banner(): BelongsTo
    {
        return $this->belongsTo(MarketingBanner::class, 'banner_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
