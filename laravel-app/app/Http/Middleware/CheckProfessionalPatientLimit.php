<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfessionalPatientLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Se não for profissional ou for admin, ignora
        if (!$user || $user->isAdministrator()) {
            return $next($request);
        }

        $plan = $user->professionalPlan;
        
        // Se não tem plano, assume que não pode cadastrar ou tem um padrão (Básico)
        if (!$plan) {
            return redirect()->back()->with('error', 'Você precisa assinar um plano profissional para vincular pacientes.');
        }

        $maxPatients = (int) $plan->max_patients;
        
        // 0 = Ilimitado
        if ($maxPatients > 0) {
            $patientCount = $user->patients()->count();
            
            if ($patientCount >= $maxPatients) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Limite de pacientes atingido para o plano atual',
                        'upgrade_url' => route('professional.billing.index')
                    ], 403);
                }

                return redirect()->back()->with('error', 'Limite de pacientes atingido para o plano atual. Sugerimos o upgrade para o plano Premium.');
            }
        }

        return $next($request);
    }
}
