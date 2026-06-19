<?php

namespace App\Http\Middleware;

use App\Models\ApiAccessLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class LogApiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('observability.api_log.enabled', true)) {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');
        foreach ((array) config('observability.api_log.ignore_paths', []) as $ignore) {
            if ($path === ltrim($ignore, '/') || str_starts_with($path, ltrim($ignore, '/').'/')) {
                return $next($request);
            }
        }

        $sampleRate = (float) config('observability.api_log.sample_rate', 1.0);
        if ($sampleRate < 1.0 && mt_rand() / mt_getrandmax() > $sampleRate) {
            return $next($request);
        }

        $started = microtime(true);
        $response = $next($request);

        if (! Schema::hasTable('api_access_logs')) {
            return $response;
        }

        try {
            $user = $request->user();
            $tokenId = null;
            if ($user && method_exists($user, 'currentAccessToken')) {
                $tokenId = $user->currentAccessToken()?->id;
            }

            ApiAccessLog::create([
                'request_id' => $request->attributes->get('request_id'),
                'user_id' => $user?->id,
                'token_id' => $tokenId,
                'method' => $request->method(),
                'path' => mb_substr($path, 0, 500),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => (int) round((microtime(true) - $started) * 1000),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent() ? mb_substr((string) $request->userAgent(), 0, 500) : null,
            ]);
        } catch (\Throwable) {
            // Never break API responses due to logging failure.
        }

        return $response;
    }
}
