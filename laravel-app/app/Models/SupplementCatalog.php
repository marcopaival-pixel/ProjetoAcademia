<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplementCatalog extends Model
{
    protected $table = 'supplements_catalog';

    protected $fillable = [
        'name',
        'category',
        'default_dosage',
        'default_unit',
        'description',
        'benefits',
        'side_effects',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
