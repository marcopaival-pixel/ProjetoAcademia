<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Testimonial extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'profession',
        'avatar_path',
        'rating',
        'testimonial',
        'city',
        'state',
        'featured',
        'is_public',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'rating' => 'integer',
        'featured' => 'boolean',
        'is_public' => 'boolean',
        'approved_at' => 'datetime',
        'created_by' => 'integer',
    ];

    /**
     * Scope to only include approved testimonials.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Scope to only include public testimonials.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to only include featured testimonials.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Get the user who created this testimonial.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper to get avatar URL or fallback.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }
        
        // Generate initials avatar
        $initials = collect(explode(' ', $this->name))
            ->map(fn($n) => mb_substr($n, 0, 1))
            ->take(2)
            ->join('');

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=10b981&background=090d16&bold=true';
    }
}
