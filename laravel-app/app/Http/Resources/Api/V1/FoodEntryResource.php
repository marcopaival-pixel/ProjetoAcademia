<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\FoodEntry */
class FoodEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'meal_type' => $this->meal_type,
            'food_name' => $this->food_name,
            'amount' => $this->amount,
            'unit' => $this->unit,
            'calories' => $this->calories,
            'protein_g' => $this->protein_g,
            'carbs_g' => $this->carbs_g,
            'fat_g' => $this->fat_g,
            'entry_date' => $this->entry_date?->toDateString(),
        ];
    }
}
