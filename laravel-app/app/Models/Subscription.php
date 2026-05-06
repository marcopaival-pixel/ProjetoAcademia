<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_CANCELLED_SCHEDULED = 'cancelled_scheduled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_BLOCKED = 'blocked';

    // Status padronizados sugeridos pelo usuário (SaaS Premium)
    const STATUS_FIN_PENDENTE = 'PENDENTE';
    const STATUS_FIN_AGUARDANDO = 'AGUARDANDO';
    const STATUS_FIN_ATIVO = 'ATIVO';
    const STATUS_FIN_RECUSADO = 'RECUSADO';
    const STATUS_FIN_CANCELADO = 'CANCELADO';

    // Compatibilidade com código legado
    const FIN_ATIVO = 'ATIVO';
    const FIN_PENDENTE = 'PENDENTE';
    const FIN_ATRASADO = 'ATRASADO';
    const FIN_SUSPENSO = 'SUSPENSO';
    const FIN_BLOQUEADO = 'BLOQUEADO';

    protected $fillable = [
        'user_id', 
        'academy_company_id',
        'plan_id', 
        'gateway_id',
        'gateway_type',
        'start_date', 
        'end_date', 
        'status', 
        'payment_method',
        'billing_type',
        'max_professionals',
        'card_brand',
        'card_last_four',
        'card_expiry',
        'next_billing_date',
        'retry_count',
        'last_attempt_at',
        'pending_plan_id',
        'cancelled_at',
        'refunded_at',
        'refunded_amount',
        'reason_for_suspension',
        'days_overdue'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'last_attempt_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refunded_amount' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function pendingPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'pending_plan_id');
    }

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubscriptionLog::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_CANCELLED_SCHEDULED, self::FIN_ATIVO, self::STATUS_FIN_ATIVO]) && 
               ($this->end_date === null || $this->end_date->isFuture() || $this->end_date->isToday());
    }

    /**
     * Retorna o status financeiro padronizado.
     */
    public function getFinancialStatus(): string
    {
        if ($this->status === self::STATUS_ACTIVE || $this->status === self::FIN_ATIVO) {
            return self::FIN_ATIVO;
        }

        if ($this->days_overdue >= 15 || $this->status === self::FIN_BLOQUEADO || $this->status === self::STATUS_BLOCKED) {
            return self::FIN_BLOQUEADO;
        }

        if ($this->days_overdue >= 10 || $this->status === self::FIN_SUSPENSO || $this->status === self::STATUS_SUSPENDED) {
            return self::FIN_SUSPENSO;
        }

        if ($this->days_overdue >= 5 || $this->status === self::FIN_ATRASADO || $this->status === self::STATUS_OVERDUE) {
            return self::FIN_ATRASADO;
        }

        if ($this->status === self::STATUS_PENDING || $this->status === self::FIN_PENDENTE) {
            return self::FIN_PENDENTE;
        }

        return strtoupper($this->status);
    }
}
