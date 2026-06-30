<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPlatformAdmin
{
    /**
     * Rotas sensíveis da plataforma (LGPD global, backups, exportações em massa).
     * Exige is_admin — administradores de tenant com permissões não bastam.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->is_admin) {
            abort(403, 'Acesso reservado à administração da plataforma.');
        }

        return $next($request);
    }
}
