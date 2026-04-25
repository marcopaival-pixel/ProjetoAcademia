<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class FoodEntry extends Model
{
    use Traits\BelongsToCompany;
    protected $companyColumn = 'user_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'entry_date',
        'meal_type',
        'food_name',
        'amount',
        'unit',
        'calories',
        'protein_g',
        'carbs_g',
        'fat_g',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'calories' => 'integer',
        'protein_g' => 'float',
        'carbs_g' => 'float',
        'fat_g' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInDateRange(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('entry_date', [$start, $end]);
    }
}
