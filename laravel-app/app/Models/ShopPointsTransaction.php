<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopPointsTransaction extends Model
{
    public const TYPE_EARN = 'earn';

    public const TYPE_REDEEM = 'redeem';

    public const TYPE_REFUND = 'refund';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',
        'points',
        'cashback_amount',
        'description',
        'source',
        'source_id',
        'expires_at',
    ];

    protected $casts = [
        'cashback_amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(ShopPointsWallet::class, 'wallet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
