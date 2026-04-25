<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Representa uma assinatura recorrente do Mercado Pago.
 * Tabela correspondente: mercadopago_subscriptions
 */
class MercadoPagoSubscription extends Model
{
    use Traits\BelongsToCompany;
    protected $companyColumn = 'user_id';
    protected $table = 'mercadopago_subscriptions';

    // mp_preapproval_id é a chave primária na migration 2025_03_31
    protected $primaryKey = 'mp_preapproval_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'mp_preapproval_id',
        'user_id',
        'plan_code',
        'status',
        'coupon_id',
    ];

    /**
     * Relacionamento com o usuário dono da assinatura.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento opcional com um cupom aplicado.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
