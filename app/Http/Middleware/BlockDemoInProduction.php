<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Impede uso do modo demonstração em ambiente de produção.
 */
class BlockDemoInProduction
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            abort(404);
        }

        return $next($request);
    }
}
