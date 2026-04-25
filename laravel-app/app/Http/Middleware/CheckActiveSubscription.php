<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasPremiumAccess()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Você precisa de uma assinatura ativa para usar esta funcionalidade.'
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Esta funcionalidade está disponível apenas para usuários com assinatura ativa.');
        }

        return $next($request);
    }
}
