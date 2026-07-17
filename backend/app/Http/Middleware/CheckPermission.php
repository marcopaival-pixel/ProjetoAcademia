<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->isBlocked()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua conta está bloqueada.');
        }

        if (!$user->hasPermission($permission)) {
            abort(403, 'Acesso não autorizado para esta funcionalidade.');
        }

        return $next($request);
    }
}
