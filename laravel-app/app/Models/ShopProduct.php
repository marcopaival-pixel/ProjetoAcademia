<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopProduct extends Model
{
    use BelongsToCompany;
    use SoftDeletes;

    protected $fillable = [
        'academy_company_id',
        'vendor_id',
        'category_id',
        'supplier_id',
        'type',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'cost_price',
        'manage_stock',
        'stock_quantity',
        'stock_alert_threshold',
        'weight',
        'dimensions',
        'downloadable_file',
        'download_limit',
        'download_expiry_days',
        'requires_scheduling',
        'ai_tags',
        'goal_types',
        'is_featured',
        'is_active',
        'status',
        'published_at',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'sale_price'     => 'decimal:2',
        'cost_price'     => 'decimal:2',
        'manage_stock'   => 'boolean',
        'is_featured'    => 'boolean',
        'is_active'      => 'boolean',
        'requires_scheduling' => 'boolean',
        'dimensions'     => 'array',
        'ai_tags'        => 'array',
        'goal_types'     => 'array',
        'published_at'   => 'datetime',
    ];

    // Tipos de produto
    const TYPE_PHYSICAL = 'physical';
    const TYPE_DIGITAL  = 'digital';
    const TYPE_SERVICE  = 'service';

    // Status de publicação
    const STATUS_DRAFT          = 'draft';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_PUBLISHED      = 'published';
    const STATUS_ARCHIVED       = 'archived';

    // ── Relacionamentos ────────────────────────────────────────────────────────

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(ShopVendor::class, 'vendor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShopCategory::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(ShopSupplier::class, 'supplier_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ShopProductImage::class, 'product_id')->orderBy('sort_order');
    }

    public function primaryImage(): ?ShopProductImage
    {
        return $this->images->firstWhere('is_primary', true)
            ?? $this->images->first();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class, 'product_id');
    }

    public function wishlistEntries(): HasMany
    {
        return $this->hasMany(ShopWishlist::class, 'product_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('manage_stock', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function currentPrice(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function isPhysical(): bool
    {
        return $this->type === self::TYPE_PHYSICAL;
    }

    public function isDigital(): bool
    {
        return $this->type === self::TYPE_DIGITAL;
    }

    public function isService(): bool
    {
        return $this->type === self::TYPE_SERVICE;
    }

    public function isInStock(): bool
    {
        if (! $this->manage_stock) {
            return true;
        }

        return ($this->stock_quantity ?? 0) > 0;
    }

    public function hasLowStock(): bool
    {
        if (! $this->manage_stock || $this->stock_alert_threshold === null) {
            return false;
        }

        return ($this->stock_quantity ?? 0) <= $this->stock_alert_threshold;
    }
}
