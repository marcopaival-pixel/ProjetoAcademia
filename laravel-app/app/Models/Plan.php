<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name', 
        'description',
        'type',
        'price', 
        'ai_credits',
        'max_students',
        'max_workouts',
        'max_diets',
        'max_assessments',
        'max_patients',
        'max_professionals',
        'is_corporate',
        'price_per_professional',
        'min_professionals',
        'commission_rate',
        'trial_days',
    ];

    protected $casts = [
        'is_corporate' => 'boolean',
        'price' => 'decimal:2',
        'price_per_professional' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    public function planFeatures(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'plan_permissions', 'plan_id', 'permission_id');
    }

    public function hasFeature(string $feature): bool
    {
        return $this->planFeatures()
            ->where('feature_key', $feature)
            ->where('is_enabled', true)
            ->exists();
    }
}
