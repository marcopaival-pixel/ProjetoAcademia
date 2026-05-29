<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FoodEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutritionDiaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $date = $request->query('date', now()->toDateString());

        $entries = FoodEntry::query()
            ->where('user_id', $user->id)
            ->whereDate('entry_date', $date)
            ->orderBy('id')
            ->get();

        $totals = [
            'calories' => (int) $entries->sum('calories'),
            'protein_g' => round((float) $entries->sum('protein_g'), 1),
            'carbs_g' => round((float) $entries->sum('carbs_g'), 1),
            'fat_g' => round((float) $entries->sum('fat_g'), 1),
        ];

        return response()->json([
            'date' => $date,
            'totals' => $totals,
            'entries' => $entries->map(fn (FoodEntry $entry) => [
                'id' => $entry->id,
                'meal_type' => $entry->meal_type,
                'food_name' => $entry->food_name,
                'amount' => $entry->amount,
                'unit' => $entry->unit,
                'calories' => $entry->calories,
                'protein_g' => $entry->protein_g,
                'carbs_g' => $entry->carbs_g,
                'fat_g' => $entry->fat_g,
            ])->values(),
        ]);
    }
}
