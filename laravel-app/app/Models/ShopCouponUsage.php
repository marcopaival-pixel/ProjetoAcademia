<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCouponUsage extends Model
{
    protected $fillable = [
        'coupon_id',
        'order_id',
        'user_id',
        'discount_applied',
    ];

    protected $casts = [
        'discount_applied' => 'decimal:2',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(ShopCoupon::class, 'coupon_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
