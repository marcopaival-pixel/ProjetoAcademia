<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiFeatureCost extends Model
{
    protected $fillable = [
        'feature_code',
        'feature_name',
        'credits_required',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
