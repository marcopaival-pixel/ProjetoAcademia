<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'is_premium_user' => $request->user()->hasPremiumAccess(),
                'rankings' => [
                    'consistency' => [],
                    'strength' => [],
                    'nutrition' => [],
                    'elite' => [],
                ],
                'badges' => [],
            ],
        ]);
    }
}
