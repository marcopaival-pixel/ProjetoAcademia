<?php

namespace App\Http\Middleware;

use App\Support\Api\V1ErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiRole
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return V1ErrorResponse::make('Não autenticado.', 401, 'unauthenticated');
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $role) => array_map('trim', explode(',', $role)))
            ->filter()
            ->values()
            ->all();

        foreach ($allowed as $role) {
            if ($role === 'professional' && $user->isProfessional()) {
                return $next($request);
            }

            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return V1ErrorResponse::make(
            'Acesso negado para este perfil.',
            403,
            'forbidden'
        );
    }
}
