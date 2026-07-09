<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FiscalInvoice extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_BLOCKED = 'blocked';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_ISSUED = 'issued';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCEL_PENDING = 'cancel_pending';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'payment_id',
        'user_id',
        'academy_company_id',
        'status',
        'provider',
        'provider_invoice_id',
        'invoice_number',
        'verification_code',
        'official_url',
        'pdf_url',
        'xml_url',
        'gross_amount',
        'discount_amount',
        'net_amount',
        'iss_rate',
        'iss_amount',
        'service_code',
        'service_description',
        'attempts',
        'last_error',
        'last_attempt_at',
        'issued_at',
        'cancelled_at',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'iss_rate' => 'decimal:4',
        'iss_amount' => 'decimal:2',
        'last_attempt_at' => 'datetime',
        'issued_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(FiscalInvoiceLog::class);
    }
}
