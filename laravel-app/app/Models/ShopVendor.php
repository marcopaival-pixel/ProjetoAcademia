<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopVendor extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'academy_company_id',
        'user_id',
        'name',
        'slug',
        'email',
        'phone',
        'document',
        'commission_rate',
        'status',
        'approved_by',
        'approved_at',
        'bank_data',
        'metadata',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'bank_data'       => 'array',
        'metadata'        => 'array',
        'approved_at'     => 'datetime',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_ACTIVE    = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_REJECTED  = 'rejected';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ShopProduct::class, 'vendor_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
