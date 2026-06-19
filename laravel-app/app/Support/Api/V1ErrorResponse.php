<?php

namespace App\Support\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class V1ErrorResponse
{
    public static function isApiV1Request(Request $request): bool
    {
        return $request->is('api/v1') || $request->is('api/v1/*');
    }

    /**
     * @param  array<string, mixed>  $errors
     */
    public static function make(string $message, int $status, ?string $code = null, array $errors = []): JsonResponse
    {
        $payload = [
            'error' => [
                'message' => $message,
            ],
        ];

        if ($code !== null) {
            $payload['error']['code'] = $code;
        }

        if ($errors !== []) {
            $payload['error']['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
