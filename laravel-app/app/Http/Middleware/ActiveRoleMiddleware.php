<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Services\Context\CurrentContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActiveRoleMiddleware
{
    protected CurrentContext $context;

    public function __construct(CurrentContext $context)
    {
        $this->context = $context;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        $activeRole = $request->header('X-Active-Role');
        $activeTenantId = $request->header('X-Active-Tenant');
        $activeContextId = $request->header('X-Active-Context-ID');

        if ($activeContextId) {
            if (str_starts_with($activeContextId, 'professional:')) {
                $profId = (int) substr($activeContextId, 13);
                $request->attributes->set('active_professional_id', $profId);
                session(['active_professional_id' => $profId]);
            } elseif (str_starts_with($activeContextId, 'clinic:')) {
                $clinicId = (int) substr($activeContextId, 7);
                $request->attributes->set('active_clinic_id', $clinicId);
                session(['active_clinic_id' => $clinicId]);
            } elseif ($activeContextId === 'personal') {
                $request->attributes->set('active_personal_context', true);
                session(['active_personal_context' => true]);
                session()->forget(['active_professional_id', 'active_clinic_id']);
            }
        }

        $tenant = null;

        // Se o aplicativo passou um role
        if ($activeRole) {
            // 1. Validar se o usuário tem a role no geral (ou no tenant)

            if ($activeTenantId) {
                // Se passou tenant, valida se o usuário tem a role NESTE tenant (pivot organization_user)
                $tenant = Organization::find($activeTenantId);
                if (! $tenant) {
                    return response()->json(['error' => 'Invalid tenant context'], 403);
                }

                $hasRoleInTenant = $user->organizations()
                    ->where('organization_id', $activeTenantId)
                    ->wherePivot('role', $activeRole)
                    ->wherePivot('is_active', true)
                    ->exists();

                if (! $hasRoleInTenant) {
                    return response()->json(['error' => 'Unauthorized role for this tenant context'], 403);
                }
            } else {
                // Se não passou tenant, valida se o usuário tem a role global (Spatie Permission/Roles ou nossa base legada)
                if (! $user->hasRole($activeRole)) {
                    // Fallback para caso seja 'paciente' e esteja salvo de outra forma
                    if ($activeRole === 'paciente' && ! $user->hasRole('paciente')) {
                        return response()->json(['error' => 'Unauthorized role context'], 403);
                    }
                    if ($activeRole !== 'paciente') {
                        return response()->json(['error' => 'Unauthorized role context'], 403);
                    }
                }
            }
        }

        // Define o contexto de injeção de dependência global para os Controllers
        $this->context->setContext($activeRole, $tenant);

        return $next($request);
    }
}
