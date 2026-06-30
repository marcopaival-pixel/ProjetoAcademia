<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ShopOrder extends Model
{
    use BelongsToCompany;
    use SoftDeletes;

    protected $fillable = [
        'academy_company_id',
        'user_id',
        'coupon_id',
        'order_number',
        'status',
        'subtotal',
        'discount_amount',
        'shipping_amount',
        'tax_amount',
        'total',
        'points_earned',
        'cashback_amount',
        'payment_method',
        'payment_gateway',
        'gateway_payment_id',
        'gateway_status',
        'shipping_method',
        'shipping_address',
        'tracking_code',
        'pickup_at',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
        'notes',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'total'           => 'decimal:2',
        'cashback_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'pickup_at'       => 'datetime',
        'paid_at'         => 'datetime',
        'shipped_at'      => 'datetime',
        'delivered_at'    => 'datetime',
        'cancelled_at'    => 'datetime',
    ];

    const STATUS_PENDING    = 'pending';
    const STATUS_PAID       = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_REFUNDED   = 'refunded';

    // ── Relacionamentos ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(ShopCoupon::class, 'coupon_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class, 'order_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return in_array($this->status, [
            self::STATUS_PAID,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_COMPLETED,
        ]);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PAID]);
    }

    public function hasPhysicalItems(): bool
    {
        return $this->items->contains(fn ($item) => $item->product_type === ShopProduct::TYPE_PHYSICAL);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'Aguardando pagamento',
            self::STATUS_PAID       => 'Pago',
            self::STATUS_PROCESSING => 'Em processamento',
            self::STATUS_SHIPPED    => 'Enviado',
            self::STATUS_DELIVERED  => 'Entregue',
            self::STATUS_COMPLETED  => 'Concluído',
            self::STATUS_CANCELLED  => 'Cancelado',
            self::STATUS_REFUNDED   => 'Reembolsado',
            default                 => Str::title($this->status),
        };
    }

    // ── Boot ───────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ShopOrder $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $year    = now()->year;
        $latest  = static::whereYear('created_at', $year)->max('id') ?? 0;
        $sequence = str_pad($latest + 1, 5, '0', STR_PAD_LEFT);

        return "SHP-{$year}-{$sequence}";
    }
}
