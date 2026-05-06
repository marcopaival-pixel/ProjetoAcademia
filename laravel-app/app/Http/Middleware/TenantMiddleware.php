<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        \Log::debug('TenantMiddleware check | User: ' . $user->email . ' | Path: ' . $request->path() . ' | Context: ' . (\App\Support\TenantContext::has() ? 'HAS (' . \App\Support\TenantContext::get() . ')' : 'NO'));

        // 1. Regra ALUNO: Isolamento Total
        // Se o usuário for apenas aluno, ele não deve ter acesso a rotas de clínica/pacientes
        if ($user->hasRole('aluno') && !$user->is_admin && !$user->hasRole(['professional', 'receptionist'])) {
            if ($request->is('admin/clinica/*') || $request->is('portal-paciente/*')) {
                abort(403, 'Acesso restrito a assinantes do portal clínico.');
            }
        }

        // 2. Administradores globais ignoram a seleção de clínica obrigatória
        if ($user->is_admin && !session()->has('impersonated_clinic_id')) {
            return $next($request);
        }

        // 3. Gerenciamento de Contexto (Tenant)
        $clinicId = session('active_clinic_id') ?? session('impersonated_clinic_id');

        if (!$clinicId) {
            // Tenta auto-seleção se houver apenas um vínculo
            $clinics = \DB::table('clinic_user')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->pluck('academy_company_id');

            if ($clinics->count() === 1) {
                $clinicId = $clinics->first();
                session(['active_clinic_id' => $clinicId]);
            } elseif ($clinics->count() > 1) {
                // Múltiplas clínicas: obriga seleção para pacientes e profissionais
                // Exceção: Permite logout e rotas de seleção sem redirecionar
                $exceptions = ['clinic.selector', 'clinic.select', 'logout', 'admin.logout'];
                if (!$request->routeIs($exceptions) && !$request->is('api/*')) {
                    \Log::debug('TenantMiddleware | Redirecting to clinic.selector | User: ' . $user->email . ' | Target Path: ' . $request->path());
                    return redirect()->route('clinic.selector');
                }
            } else {
                // Fallback para legado (coluna academy_company_id na tabela users)
                if ($user->academy_company_id) {
                    $clinicId = $user->academy_company_id;
                    session(['active_clinic_id' => $clinicId]);
                }
            }
        }

        // 4. Ativa o Contexto Global
        if ($clinicId) {
            \Log::debug('TenantMiddleware checking for: ' . $user->email . ' | Path: ' . $request->path() . ' | Context: ' . (\App\Support\TenantContext::has() ? 'HAS CONTEXT (' . \App\Support\TenantContext::get() . ')' : 'NO CONTEXT'));
            \App\Support\TenantContext::set((int) $clinicId);
            
            // Validação de Segurança: O usuário realmente tem acesso a esta clínica?
            if (!$user->is_admin && $user->academy_company_id != $clinicId) {
                $hasAccess = \DB::table('clinic_user')
                    ->where('user_id', $user->id)
                    ->where('academy_company_id', $clinicId)
                    ->where('status', 'active')
                    ->exists();
                
                if (!$hasAccess) {
                    session()->forget('active_clinic_id');
                    abort(403, 'Você não tem permissão para acessar esta unidade.');
                }
            }
        }

        return $next($request);
    }
}
