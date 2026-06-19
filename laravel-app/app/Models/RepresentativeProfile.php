<?php

namespace App\Models;

use App\Models\Traits\FiltersByRepresentative;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepresentativeProfile extends Model
{
    use FiltersByRepresentative, HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'commission_rate',
        'max_discount_rate',
        'code_expires_at',
        'max_code_usages',
        'current_code_usages',
        'payment_rules',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'max_discount_rate' => 'decimal:2',
        'code_expires_at' => 'datetime',
        'max_code_usages' => 'integer',
        'current_code_usages' => 'integer',
        'payment_rules' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se o código é válido no momento.
     */
    public function isValid(): bool
    {
        if ($this->code_expires_at && $this->code_expires_at->isPast()) {
            return false;
        }

        if ($this->max_code_usages !== null && $this->current_code_usages >= $this->max_code_usages) {
            return false;
        }

        return true;
    }

    /**
     * Valida um desconto oferecido comparado com o limite.
     */
    public function canApplyDiscount(float $requestedDiscountRate): bool
    {
        return $requestedDiscountRate <= (float) $this->max_discount_rate;
    }

    /**
     * Calcula o valor da comissão com base num valor líquido cobrado.
     */
    public function calculateCommission(float $paidAmount): float
    {
        return ($paidAmount * (float) $this->commission_rate) / 100;
    }

    /**
     * Incrementa o uso do código
     */
    public function incrementUsage(): void
    {
        $this->increment('current_code_usages');
    }
}
