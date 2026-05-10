<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingBannerDismissal extends Model
{
    protected $fillable = [
        'banner_id',
        'user_id',
        'dont_show_again',
    ];

    public function banner(): BelongsTo
    {
        return $this->belongsTo(MarketingBanner::class, 'banner_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
