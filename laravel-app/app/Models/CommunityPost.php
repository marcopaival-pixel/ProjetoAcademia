<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CommunityPost extends Model
{
    use SoftDeletes, Traits\FiltersByProfessional;

    protected $fillable = [
        'user_id',
        'academy_company_id',
        'content',
        'status',
        'visibility',
        'activity_status',
        'hashtags',
        'is_pinned',
        'scheduled_at',
    ];

    protected $casts = [
        'hashtags' => 'array',
        'is_pinned' => 'boolean',
        'scheduled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academyCompany(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(CommunityPostMedia::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CommunityComment::class, 'post_id');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(CommunityReaction::class, 'reactable');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CommunityReport::class, 'post_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeClinic($query, $clinicId)
    {
        return $query->where('visibility', 'clinic')->where('academy_company_id', $clinicId);
    }
}
