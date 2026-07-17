<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCreditPackage extends Model
{
    use HasFactory;

    protected $table = 'ai_credits_packages';

    protected $fillable = [
        'name',
        'credits',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
