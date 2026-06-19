<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ClientErrorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ClientErrorController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (! config('observability.client_errors.enabled', true)) {
            return response()->json(['ok' => true]);
        }

        $validated = $request->validate([
            'type' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:2000'],
            'stack' => ['nullable', 'string', 'max:10000'],
            'url' => ['nullable', 'string', 'max:500'],
        ]);

        if (! Schema::hasTable('client_error_logs')) {
            return response()->json(['ok' => true]);
        }

        ClientErrorLog::create([
            'user_id' => $request->user()?->id,
            'type' => $validated['type'] ?? 'error',
            'message' => $validated['message'],
            'stack' => $validated['stack'] ?? null,
            'url' => $validated['url'] ?? null,
            'user_agent' => $request->userAgent() ? mb_substr((string) $request->userAgent(), 0, 500) : null,
            'ip' => $request->ip(),
        ]);

        return response()->json(['ok' => true]);
    }
}
