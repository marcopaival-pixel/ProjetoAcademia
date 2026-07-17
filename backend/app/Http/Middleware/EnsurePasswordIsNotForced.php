<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsNotForced
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->force_password_change && !$request->routeIs('password.change.force*') && !$request->routeIs('logout')) {
            return redirect()->route('password.change.force')
                ->with('warning', 'Você deve alterar sua senha antes de continuar.');
        }

        return $next($request);
    }
}
