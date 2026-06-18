<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use App\Support\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use BelongsToCompany;
    use SoftDeletes;

    public const STATUS_ACTIVE = SubscriptionStatus::ACTIVE;

    public const STATUS_PENDING = SubscriptionStatus::PENDING;

    public const STATUS_OVERDUE = SubscriptionStatus::OVERDUE;

    public const STATUS_SUSPENDED = SubscriptionStatus::SUSPENDED;

    public const STATUS_CANCELLED_SCHEDULED = SubscriptionStatus::CANCELLED_SCHEDULED;

    public const STATUS_CANCELLED = SubscriptionStatus::CANCELLED;

    public const STATUS_EXPIRED = SubscriptionStatus::EXPIRED;

    public const STATUS_BLOCKED = SubscriptionStatus::BLOCKED;

    public const STATUS_FIN_PENDENTE = SubscriptionStatus::PENDING;

    public const STATUS_FIN_AGUARDANDO = SubscriptionStatus::TRIALING;

    public const STATUS_FIN_ATIVO = SubscriptionStatus::ACTIVE;

    public const STATUS_FIN_RECUSADO = SubscriptionStatus::DECLINED;

    public const STATUS_FIN_CANCELADO = SubscriptionStatus::CANCELLED;

    /** @deprecated Use SubscriptionStatus::ACTIVE */
    public const FIN_ATIVO = SubscriptionStatus::ACTIVE;

    /** @deprecated Use SubscriptionStatus::PENDING */
    public const FIN_PENDENTE = SubscriptionStatus::PENDING;

    /** @deprecated Use SubscriptionStatus::OVERDUE */
    public const FIN_ATRASADO = SubscriptionStatus::OVERDUE;

    /** @deprecated Use SubscriptionStatus::SUSPENDED */
    public const FIN_SUSPENSO = SubscriptionStatus::SUSPENDED;

    /** @deprecated Use SubscriptionStatus::BLOCKED */
    public const FIN_BLOQUEADO = SubscriptionStatus::BLOCKED;

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
        'days_overdue',
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

    protected static function booted(): void
    {
        static::saving(function (Subscription $subscription) {
            if ($subscription->status !== null) {
                $subscription->status = SubscriptionStatus::normalize($subscription->status);
            }
        });
    }

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

    public function canonicalStatus(): string
    {
        return SubscriptionStatus::normalize($this->status);
    }

    public function scopeWhereCanonicalStatus($query, string ...$statuses)
    {
        return SubscriptionStatus::scopeWhereStatusIn($query, $statuses);
    }

    public function isActive(): bool
    {
        return SubscriptionStatus::grantsPremiumAccess($this->status, $this->end_date);
    }

    /**
     * Status financeiro legível (baseado no status canônico + dias em atraso).
     */
    public function getFinancialStatus(): string
    {
        $status = $this->canonicalStatus();

        if ($this->days_overdue >= 15 || $status === SubscriptionStatus::BLOCKED) {
            return SubscriptionStatus::BLOCKED;
        }

        if ($this->days_overdue >= 10 || $status === SubscriptionStatus::SUSPENDED) {
            return SubscriptionStatus::SUSPENDED;
        }

        if ($this->days_overdue >= 5 || $status === SubscriptionStatus::OVERDUE) {
            return SubscriptionStatus::OVERDUE;
        }

        return $status;
    }
}
