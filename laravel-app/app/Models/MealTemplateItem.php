<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealTemplateItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'meal_template_id',
        'meal_type',
        'food_name',
        'calories',
        'protein_g',
        'carbs_g',
        'fat_g',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'calories' => 'integer',
            'protein_g' => 'float',
            'carbs_g' => 'float',
            'fat_g' => 'float',
            'position' => 'integer',
        ];
    }

    public function mealTemplate(): BelongsTo
    {
        return $this->belongsTo(MealTemplate::class);
    }
}
