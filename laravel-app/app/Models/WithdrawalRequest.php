<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'amount',
        'pix_key',
        'bank_info',
        'status',
        'admin_notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDENTE = 'PENDENTE';
    const STATUS_APROVADO = 'APROVADO';
    const STATUS_PAGO = 'PAGO';
    const STATUS_RECUSADO = 'RECUSADO';

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representative_id');
    }
}
