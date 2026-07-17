<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradePopup extends Model
{
    protected $fillable = [
        'feature_code',
        'title',
        'message',
        'benefits',
        'button_text',
        'image_url',
    ];

    protected $casts = [
        'benefits' => 'array',
    ];
}
