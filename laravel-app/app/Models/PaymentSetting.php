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
        'public_key',
        'access_token',
        'webhook_secret',
        'enable_credit_card',
        'enable_pix',
        'enable_boleto',
        'boleto_expiration_days',
        'pix_expiration_minutes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'public_key' => 'encrypted',
            'access_token' => 'encrypted',
            'webhook_secret' => 'encrypted',
            'enable_credit_card' => 'boolean',
            'enable_pix' => 'boolean',
            'enable_boleto' => 'boolean',
            'boleto_expiration_days' => 'integer',
            'pix_expiration_minutes' => 'integer',
        ];
    }
}
