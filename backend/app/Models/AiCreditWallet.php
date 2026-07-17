<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiCreditWallet extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'balance',
        'monthly_allowance',
        'extra_credits',
        'renewal_date',
        'expires_at',
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'expires_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
