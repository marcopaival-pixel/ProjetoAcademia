<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class EnsureProfessionalIsSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Somente para pacientes
        if ($user && $user->hasRole('paciente')) {
            
            // Se já tiver um profissional selecionado na sessão, continua
            if (Session::has('active_professional_id')) {
                return $next($request);
            }

            // Se for uma das rotas de seleção ou ativação, permite
            $allowedRoutes = [
                'patient.professional.selection',
                'patient.professional.select',
                'patient.dashboard.choice',
                'patient.unified.dashboard',
                'patient.activate.show',
                'patient.activate.process',
                'patient.profile.complete',
                'patient.profile.store',
                'logout',
            ];

            if ($request->routeIs($allowedRoutes)) {
                return $next($request);
            }

            // Se tiver apenas 1 profissional, seleciona automaticamente
            $professionals = $user->professionals()->wherePivot('status', 'Sim')->get();
            if ($professionals->count() === 1) {
                Session::put('active_professional_id', $professionals->first()->id);
                return $next($request);
            }

            // Se tiver mais de 1 (ou zero - embora zero devesse ser tratado), redireciona para seleção
            if ($professionals->count() > 1) {
                return redirect()->route('patient.professional.selection');
            }
        }

        return $next($request);
    }
}
