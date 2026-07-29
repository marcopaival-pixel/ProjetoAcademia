<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    protected $fillable = [
        'professional_id',
        'patient_id',
        'code',
        'discount_type',
        'discount_value',
        'expiration_date',
        'max_uses',
        'used_count',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'discount_value' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
    ];

    public function professional(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function patient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function usages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               (!$this->expiration_date || $this->expiration_date->isFuture()) && 
               ($this->max_uses === 0 || $this->used_count < $this->max_uses);
    }

    public function isValidForUser(int $userId): bool
    {
        if (! $this->isActive()) return false;
        
        // If patient_id is set, only that patient can use it
        if ($this->patient_id && $this->patient_id != $userId) return false;
        
        return true;
    }

    public function calculateDiscount(float $originalPrice): float
    {
        if ($this->discount_type === 'percentage') {
            return ($originalPrice * (float)$this->discount_value) / 100;
        }
        
        return min($originalPrice, (float)$this->discount_value);
    }

    public function apply(float $price): float
    {
        return max(0, $price - $this->calculateDiscount($price));
    }

    public function markAsUsed(int $userId): bool
    {
        return DB::transaction(function () use ($userId) {
            $coupon = self::query()->whereKey($this->getKey())->lockForUpdate()->first();

            if (! $coupon || ! $coupon->isValidForUser($userId)) {
                return false;
            }

            $coupon->increment('used_count');

            if ($coupon->max_uses > 0 && $coupon->used_count >= $coupon->max_uses) {
                $coupon->update(['status' => 'expired']);
            }

            return true;
        });
    }
}
