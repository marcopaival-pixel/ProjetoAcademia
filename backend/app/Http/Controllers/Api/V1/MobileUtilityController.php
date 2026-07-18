<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobileUtilityController extends Controller
{
    public function registerDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['nullable', 'string', 'max:512'],
            'platform' => ['nullable', 'string', 'max:40'],
            'device_id' => ['nullable', 'string', 'max:255'],
        ]);

        Log::info('Mobile device registration received', [
            'user_id' => $request->user()->id,
            'platform' => $validated['platform'] ?? 'android',
            'device_id' => $validated['device_id'] ?? null,
        ]);

        return response()->json(['data' => ['registered' => true]]);
    }

    public function notificationCounts(): JsonResponse
    {
        return response()->json([
            'data' => [
                'emails' => 0,
                'messages' => 0,
                'total' => 0,
            ],
        ]);
    }

    public function clientError(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:4000'],
            'stack' => ['nullable', 'string'],
            'url' => ['nullable', 'string', 'max:1000'],
        ]);

        Log::warning('Mobile client error', [
            'user_id' => $request->user()?->id,
            'payload' => $validated,
        ]);

        return response()->json(['ok' => true]);
    }
}
