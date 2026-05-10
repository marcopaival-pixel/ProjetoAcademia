<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'environment',
        'client_id',
        'client_secret',
        'public_key',
        'access_token',
        'webhook_secret',
        'webhook_url',
        'timeout',
        'priority',
        'enable_credit_card',
        'enable_pix',
        'enable_boleto',
        'boleto_expiration_days',
        'pix_expiration_minutes',
        'status',
        'penalty_percent',
        'interest_percent',
        'discount_percent',
        'tolerance_days',
    ];

    protected function casts(): array
    {
        return [
            'client_id' => 'encrypted',
            'client_secret' => 'encrypted',
            'public_key' => 'encrypted',
            'access_token' => 'encrypted',
            'webhook_secret' => 'encrypted',
            'enable_credit_card' => 'boolean',
            'enable_pix' => 'boolean',
            'enable_boleto' => 'boolean',
            'boleto_expiration_days' => 'integer',
            'pix_expiration_minutes' => 'integer',
            'timeout' => 'integer',
            'priority' => 'integer',
            'penalty_percent' => 'decimal:2',
            'interest_percent' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'tolerance_days' => 'integer',
        ];
    }
}
