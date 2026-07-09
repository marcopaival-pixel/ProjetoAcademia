<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalSetting extends Model
{
    protected $fillable = [
        'provider',
        'environment',
        'issuer_cnpj',
        'issuer_legal_name',
        'municipal_registration',
        'tax_regime',
        'cnae',
        'municipal_service_code',
        'iss_rate',
        'certificate_reference',
        'api_token',
        'is_active',
        'extra',
    ];

    protected $casts = [
        'iss_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'extra' => 'array',
    ];

    public static function active(): ?self
    {
        return self::query()->where('is_active', true)->latest('id')->first();
    }
}
