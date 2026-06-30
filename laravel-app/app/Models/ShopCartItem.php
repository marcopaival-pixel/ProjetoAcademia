<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity'   => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShopCart::class, 'cart_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id');
    }

    public function lineTotal(): float
    {
        return round((float) $this->unit_price * $this->quantity, 2);
    }
}
