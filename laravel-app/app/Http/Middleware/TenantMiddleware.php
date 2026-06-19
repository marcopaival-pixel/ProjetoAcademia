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

        if (config('app.debug')) {
            \Log::debug('TenantMiddleware', [
                'user' => $user->email,
                'path' => $request->path(),
                'clinic_context' => \App\Support\TenantContext::get(),
            ]);
        }

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
            // Se o usuário já tem uma clínica preferida/única setada no perfil
            if ($user->clinic_id) {
                $clinicId = $user->clinic_id;
                session(['active_clinic_id' => $clinicId]);
            } else {
                // Tenta auto-seleção se houver apenas uma clínica vinculada à empresa do usuário
                $clinics = \App\Models\Clinic::where('academy_company_id', $user->academy_company_id)
                    ->where('is_active', true)
                    ->get();

                if ($clinics->count() === 1) {
                    $clinicId = $clinics->first()->id;
                    session(['active_clinic_id' => $clinicId]);
                    $user->update(['clinic_id' => $clinicId]); // Salva como preferência
                } elseif ($clinics->count() > 1) {
                    if (! $request->routeIs('clinic.selector', 'clinic.select', 'logout', 'logout.*')) {
                        return redirect()->route('clinic.selector')
                            ->with('warning', 'Selecione a clínica ativa para continuar.');
                    }
                } else {
                    // Fallback para legado: tenta achar a clínica que representa a empresa
                    $legacyClinic = \App\Models\Clinic::where('slug', $user->academyCompany?->slug)->first();
                    if ($legacyClinic) {
                        $clinicId = $legacyClinic->id;
                        session(['active_clinic_id' => $clinicId]);
                        $user->update(['clinic_id' => $clinicId]);
                    }
                }
            }
        }

        // 4. Ativa o Contexto Global
        if ($clinicId) {
            \App\Support\TenantContext::set((int) $clinicId);
            
            // Validação de Segurança: O usuário pertence à empresa desta clínica?
            if (!$user->is_admin) {
                $clinic = \App\Models\Clinic::find($clinicId);
                if (!$clinic || $clinic->academy_company_id !== $user->academy_company_id) {
                    \Log::warning('TenantMiddleware | Acesso negado à clínica ' . $clinicId . ' para o usuário ' . $user->email);
                    session()->forget('active_clinic_id');
                    \App\Support\TenantContext::set(null);
                    abort(403, 'Acesso negado: clínica não pertence à sua organização.');
                }
            }
        }

        return $next($request);
    }
}
