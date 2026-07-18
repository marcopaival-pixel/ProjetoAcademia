<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NutritionGoalController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $profile = UserProfile::firstOrCreate(['user_id' => $request->user()->id]);

        return response()->json([
            'data' => [
                'goal' => $profile->goal ?? 'maintain',
                'daily_calorie_target' => $profile->daily_calorie_target,
                'protein_target_g' => $profile->protein_target_g,
                'carbs_target_g' => $profile->carbs_target_g,
                'fat_target_g' => $profile->fat_target_g,
                'water_target_ml' => $profile->water_target_ml,
            ]
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'goal' => ['nullable', 'string', Rule::in(array_keys(UserProfile::getAvailableGoals()))],
            'daily_calorie_target' => ['nullable', 'integer', 'min:500', 'max:10000'],
            'protein_target_g' => ['nullable', 'numeric', 'min:0'],
            'carbs_target_g' => ['nullable', 'numeric', 'min:0'],
            'fat_target_g' => ['nullable', 'numeric', 'min:0'],
            'water_target_ml' => ['nullable', 'integer', 'min:0'],
        ]);

        $profile = UserProfile::firstOrCreate(['user_id' => $request->user()->id]);
        $profile->update($validated);

        return response()->json([
            'data' => [
                'goal' => $profile->goal,
                'daily_calorie_target' => $profile->daily_calorie_target,
                'protein_target_g' => $profile->protein_target_g,
                'carbs_target_g' => $profile->carbs_target_g,
                'fat_target_g' => $profile->fat_target_g,
                'water_target_ml' => $profile->water_target_ml,
            ]
        ]);
    }
}
