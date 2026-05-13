<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppFeature extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'is_active',
        'show_lock',
        'show_badge',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_lock' => 'boolean',
        'show_badge' => 'boolean',
    ];

    public function limits()
    {
        return $this->hasMany(FeatureLimit::class, 'feature_id');
    }
}
