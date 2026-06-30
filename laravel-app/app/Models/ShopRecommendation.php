<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopRecommendation extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'user_id',
        'academy_company_id',
        'product_ids',
        'reason',
        'context',
        'score',
        'expires_at',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'context' => 'array',
        'score' => 'decimal:4',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
