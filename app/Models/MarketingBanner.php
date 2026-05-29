<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketingBanner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image_desktop',
        'image_mobile',
        'background_color',
        'icon',
        'primary_button_text',
        'primary_button_link',
        'secondary_button_text',
        'secondary_button_link',
        'start_date',
        'end_date',
        'priority',
        'is_active',
        'allow_dismiss',
        'dont_show_again_option',
        'display_type',
        'frequency_days',
        'segmentation',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'allow_dismiss' => 'boolean',
        'dont_show_again_option' => 'boolean',
        'segmentation' => 'array',
        'priority' => 'integer',
        'frequency_days' => 'integer',
    ];

    public function targets(): HasMany
    {
        return $this->hasMany(MarketingBannerTarget::class, 'banner_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'marketing_banner_targets', 'banner_id', 'role_id');
    }

    public function views(): HasMany
    {
        return $this->hasMany(MarketingBannerView::class, 'banner_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(MarketingBannerClick::class, 'banner_id');
    }

    public function dismissals(): HasMany
    {
        return $this->hasMany(MarketingBannerDismissal::class, 'banner_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }
}
