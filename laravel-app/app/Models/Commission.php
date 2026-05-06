<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    protected $fillable = [
        'representative_id',
        'user_id',
        'payment_id',
        'subscription_id',
        'base_amount',
        'commission_rate',
        'commission_amount',
        'status',
        'available_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'available_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDENTE = 'PENDENTE';
    const STATUS_DISPONIVEL = 'DISPONIVEL';
    const STATUS_PAGO = 'PAGO';
    const STATUS_CANCELADO = 'CANCELADO';

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representative_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
