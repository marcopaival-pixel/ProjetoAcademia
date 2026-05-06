<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditoCompra extends Model
{
    protected $table = 'creditos_compras';

    protected $fillable = [
        'user_id',
        'quantidade',
        'valor',
        'status',
        'gateway',
        'payment_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
