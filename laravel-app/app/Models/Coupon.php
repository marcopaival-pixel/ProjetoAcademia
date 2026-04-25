<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function markAsUsed(int $userId): void
    {
        $this->increment('used_count');
        
        // Optional: track details in a coupon_usages table
        // For now, just mark the coupon as inactive if it reached max uses
        if ($this->max_uses > 0 && $this->used_count >= $this->max_uses) {
            $this->update(['status' => 'expired']);
        }
    }
}
