<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Http\JsonResponse;

trait FormatsApiResponses
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $meta
     */
    protected function success(array $data = [], array $meta = [], int $status = 200): JsonResponse
    {
        $payload = ['data' => $data];
        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    protected function error(string $message, int $status = 400, ?string $code = null): JsonResponse
    {
        $payload = ['error' => ['message' => $message]];
        if ($code !== null) {
            $payload['error']['code'] = $code;
        }

        return response()->json($payload, $status);
    }
}
