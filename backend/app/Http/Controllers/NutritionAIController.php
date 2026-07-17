<?php

namespace App\Http\Controllers;

use App\Services\Nutrition\NutritionAIEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutritionAIController extends Controller
{
    public function naturalLanguage(Request $request, NutritionAIEngine $engine): JsonResponse
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:500'],
        ]);

        $result = $engine->analyzeText($request->user(), $data['text']);

        return response()->json(
            $result,
            $result['success'] ? 200 : (($result['code'] ?? null) === 'credits_exceeded' ? 402 : 422),
        );
    }

    public function processPhoto(Request $request, NutritionAIEngine $engine): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:5120'],
        ]);

        $result = $engine->analyzePhoto($request->user(), $request->file('photo'));

        return response()->json(
            $result,
            $result['success'] ? 200 : (($result['code'] ?? null) === 'credits_exceeded' ? 402 : 422),
        );
    }
}
