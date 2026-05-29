<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Food extends Model
{
    protected $table = 'foods';

    protected $fillable = [
        'name',
        'brand',
        'barcode',
        'base_amount',
        'unit',
        'data_source',
    ];

    /**
     * Relacionamento com nutrientes (valores por base_amount).
     */
    public function nutrients(): BelongsToMany
    {
        return $this->belongsToMany(Nutrient::class, 'food_nutrient')
            ->withPivot('amount');
    }

    /**
     * Calcula o nutriente proporcionalmente à quantidade consumida.
     * Fórmula: (Valor por Base / Quantidade Base) * Quantidade Consumida
     */
    public function calculateNutrient(string $slug, float $consumedAmount): float
    {
        $nutrient = $this->nutrients()->where('slug', $slug)->first();
        
        if (!$nutrient || $this->base_amount <= 0) {
            return 0.0;
        }

        return ($nutrient->pivot->amount / $this->base_amount) * $consumedAmount;
    }
}
