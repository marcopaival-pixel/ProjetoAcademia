<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPremiumAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Segurança: Admin e Staff Clínico sempre passam
        if ($user && ($user->isAdministrator() || $user->hasRole(['professional', 'manager', 'instructor', 'supervisor', 'receptionist']))) {
            return $next($request);
        }

        if (!$user || !$user->hasPremiumAccess()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Acesso Premium Necessário',
                    'message' => 'Esta funcionalidade está disponível apenas para assinantes Premium.',
                    'redirect' => route('plano')
                ], 403);
            }

            return redirect()->route('plano');
        }

        return $next($request);
    }
}
