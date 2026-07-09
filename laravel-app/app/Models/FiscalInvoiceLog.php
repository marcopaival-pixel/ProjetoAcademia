<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalInvoiceLog extends Model
{
    protected $fillable = [
        'fiscal_invoice_id',
        'payment_id',
        'user_id',
        'event',
        'status_before',
        'status_after',
        'message',
        'payload',
        'ip_address',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function fiscalInvoice(): BelongsTo
    {
        return $this->belongsTo(FiscalInvoice::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
