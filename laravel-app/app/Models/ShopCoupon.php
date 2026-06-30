<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ShopCoupon extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'academy_company_id',
        'created_by',
        'code',
        'description',
        'type',
        'discount_value',
        'minimum_order_value',
        'maximum_discount',
        'applies_to',
        'category_ids',
        'product_ids',
        'free_shipping',
        'max_uses_total',
        'max_uses_per_user',
        'used_count',
        'is_single_use',
        'campaign',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'discount_value'       => 'decimal:2',
        'minimum_order_value'  => 'decimal:2',
        'maximum_discount'     => 'decimal:2',
        'category_ids'         => 'array',
        'product_ids'          => 'array',
        'free_shipping'        => 'boolean',
        'is_single_use'        => 'boolean',
        'starts_at'            => 'datetime',
        'expires_at'           => 'datetime',
    ];

    const TYPE_PERCENTAGE    = 'percentage';
    const TYPE_FIXED         = 'fixed';
    const TYPE_FREE_SHIPPING = 'free_shipping';
    const TYPE_PRODUCT_GIFT  = 'product_gift';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(ShopCouponUsage::class, 'coupon_id');
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $this->starts_at->isAfter($now)) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses_total && $this->used_count >= $this->max_uses_total) {
            return false;
        }

        return true;
    }

    public function isValidForUser(int $userId): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($this->max_uses_per_user) {
            $usedByUser = $this->usages()->where('user_id', $userId)->count();
            if ($usedByUser >= $this->max_uses_per_user) {
                return false;
            }
        }

        return true;
    }

    public function isValidForOrder(float $orderTotal): bool
    {
        if ($this->minimum_order_value && $orderTotal < (float) $this->minimum_order_value) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            $discount = $subtotal * ((float) $this->discount_value / 100);
            if ($this->maximum_discount) {
                $discount = min($discount, (float) $this->maximum_discount);
            }
            return round($discount, 2);
        }

        if ($this->type === self::TYPE_FIXED) {
            return min($subtotal, (float) $this->discount_value);
        }

        return 0.0; // free_shipping e product_gift calculados no OrderService
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
