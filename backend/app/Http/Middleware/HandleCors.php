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

        $allowedOrigins = [
            'http://localhost:5173',
            'http://127.0.0.1:5173',
            'http://localhost:8080',
            'http://127.0.0.1:8080',
        ];

        if ($origin !== null && in_array($origin, $allowedOrigins, true)) {
            $corsHeaders = [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS, PATCH',
                'Access-Control-Allow-Headers' => 'Content-Type, X-CSRF-TOKEN, Authorization, Accept',
                'Access-Control-Max-Age' => '86400',
            ];

            if ($request->isMethod('OPTIONS')) {
                return response('', 204)->withHeaders($corsHeaders);
            }

            $response = $next($request);

            foreach ($corsHeaders as $name => $value) {
                $response->headers->set($name, $value);
            }

            return $response;
        }

        return $next($request);
    }
}
