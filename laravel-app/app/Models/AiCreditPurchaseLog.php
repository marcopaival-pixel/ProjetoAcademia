<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCreditPurchaseLog extends Model
{
    use HasFactory;

    protected $table = 'ai_credits_purchase_logs';

    protected $fillable = [
        'user_id',
        'package_name',
        'credits_amount',
        'price',
        'payment_status',
        'payment_method',
        'payment_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
