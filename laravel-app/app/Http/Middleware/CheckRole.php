<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $allowedRoles = array_map('trim', explode('|', $role));
        $hasRole = false;
        foreach ($allowedRoles as $allowedRole) {
            if ($allowedRole !== '' && $user->hasRole($allowedRole)) {
                $hasRole = true;
                break;
            }
        }

        if (! $hasRole) {
            abort(403, 'Acesso não autorizado. Perfil de '.$role.' requerido.');
        }

        return $next($request);
    }
}
