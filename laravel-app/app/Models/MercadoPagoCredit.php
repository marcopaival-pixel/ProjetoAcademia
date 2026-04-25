<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MercadoPagoCredit extends Model
{
    use Traits\BelongsToCompany;
    protected $companyColumn = 'user_id';

    protected $table = 'mercadopago_payment_credits';
    protected $primaryKey = 'mp_payment_id';
    public $incrementing = false;
    public $timestamps = false; // created_at is useCurrent in migration

    protected $fillable = [
        'mp_payment_id',
        'user_id',
        'plan_code',
        'transaction_amount',
        'currency_id',
        'coupon_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
