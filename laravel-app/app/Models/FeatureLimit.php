<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureLimit extends Model
{
    protected $fillable = [
        'plan_id',
        'feature_id',
        'limit_value',
        'limit_type',
        'action_type',
        'custom_popup_text',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function feature()
    {
        return $this->belongsTo(AppFeature::class, 'feature_id');
    }
}
