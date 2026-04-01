<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->header('Origin');

        // Permitir requisições do React local
        if (in_array($origin, [
            'http://localhost:5173',
            'http://127.0.0.1:5173',
        ])) {
            $response = $next($request);
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Authorization');
            $response->header('Access-Control-Max-Age', '86400');
            return $response;
        }

        return $next($request);
    }
}

