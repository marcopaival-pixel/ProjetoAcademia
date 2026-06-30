<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopPointsWallet extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'user_id',
        'academy_company_id',
        'balance_points',
        'balance_cashback',
        'lifetime_points_earned',
        'lifetime_cashback_earned',
        'tier',
    ];

    protected $casts = [
        'balance_cashback' => 'decimal:2',
        'lifetime_cashback_earned' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ShopPointsTransaction::class, 'wallet_id');
    }
}
