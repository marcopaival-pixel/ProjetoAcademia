<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShopOrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'product_name',
        'product_sku',
        'product_type',
        'quantity',
        'unit_price',
        'discount_amount',
        'total',
        'commission_rate',
        'commission_amount',
        'commission_status',
        'download_token',
        'download_expires_at',
        'download_count',
    ];

    protected $casts = [
        'unit_price'        => 'decimal:2',
        'discount_amount'   => 'decimal:2',
        'total'             => 'decimal:2',
        'commission_rate'   => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'quantity'          => 'integer',
        'download_count'    => 'integer',
        'download_expires_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(ShopVendor::class, 'vendor_id');
    }

    public function isDownloadable(): bool
    {
        return $this->product_type === ShopProduct::TYPE_DIGITAL
            && $this->download_token !== null;
    }

    public function canDownload(): bool
    {
        if (! $this->isDownloadable()) {
            return false;
        }

        if ($this->download_expires_at && $this->download_expires_at->isPast()) {
            return false;
        }

        $product = $this->product;
        if ($product && $product->download_limit && $this->download_count >= $product->download_limit) {
            return false;
        }

        return true;
    }

    public function generateDownloadToken(): string
    {
        $token = Str::uuid()->toString();
        $this->update(['download_token' => $token]);

        return $token;
    }
}
