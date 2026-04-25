<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatientProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->hasRole('paciente') && !$user->perfil_paciente_completo) {
            // Se estiver em uma rota que não seja a de completar o perfil ou logout, redireciona
            $allowedRoutes = [
                'patient.profile.complete',
                'patient.profile.store',
                'logout',
            ];

            if (!$request->routeIs($allowedRoutes)) {
                return redirect()->route('patient.profile.complete')
                    ->with('warning', 'Você precisa completar seu perfil antes de acessar o painel.');
            }
        }

        return $next($request);
    }
}
