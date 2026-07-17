<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleClinicImpersonation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && session()->has('impersonated_clinic_id')) {
            // Verificar inatividade (30 minutos)
            $lastActivity = session('impersonation_last_activity', session('impersonation_started_at'));
            $timeout = 30 * 60; // 30 minutos em segundos

            if (time() - $lastActivity > $timeout) {
                // Expirar sessão de impersonação
                return app(\App\Http\Controllers\Admin\AdminClinicImpersonationController::class)->stop();
            }

            // Atualizar atividade
            session(['impersonation_last_activity' => time()]);

            // Forçar o contexto da clínica no usuário autenticado
            $request->user()->academy_company_id = session('impersonated_clinic_id');
            
            // Garantir que o usuário seja tratado como admin da clínica para fins de permissão local se necessário
            // No entanto, as permissões globais do admin devem ser mantidas para que ele possa sair.
        }

        return $next($request);
    }
}
