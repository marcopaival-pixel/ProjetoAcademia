<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use SoftDeletes;
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'representative_id',
        'user_id',
        'clinic_id',
        'payment_id',
        'subscription_id',
        'base_amount',
        'commission_rate',
        'commission_type',
        'commission_amount',
        'paid_amount',
        'pending_amount',
        'status',
        'available_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
        'available_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDENTE = 'PENDENTE';
    const STATUS_AGUARDANDO_PAGAMENTO = 'AGUARDANDO_PAGAMENTO';
    const STATUS_CARENCIA = 'CARENCIA';
    const STATUS_DISPONIVEL = 'DISPONIVEL'; // LIBERADA
    const STATUS_PAGO = 'PAGO';
    const STATUS_CANCELADO = 'CANCELADO';
    const STATUS_CLAWBACK = 'CLAWBACK';

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

    public function withdrawalRequests(): BelongsToMany
    {
        return $this->belongsToMany(WithdrawalRequest::class, 'commission_withdrawal')
            ->withPivot('amount_applied')
            ->withTimestamps();
    }
}
