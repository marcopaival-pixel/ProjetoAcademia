<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiProfessional
{
    /** @var list<string> */
    private const ROLES = ['professional', 'instructor', 'supervisor', 'manager', 'receptionist'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isAdministrator() && ! $user->hasRole(self::ROLES))) {
            return response()->json(['message' => 'Acesso restrito a profissionais.'], 403);
        }

        return $next($request);
    }
}
