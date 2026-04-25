<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialLog extends Model
{
    protected $fillable = [
        'user_id',
        'academy_company_id',
        'action',
        'amount',
        'status_before',
        'status_after',
        'transaction_id',
        'origin',
        'ip_address',
        'observation',
        'payload'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academyCompany(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class);
    }
}
