<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Nutrient extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'unit',
        'is_main',
    ];

    public function foods(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'food_nutrient')
            ->withPivot('amount');
    }
}
