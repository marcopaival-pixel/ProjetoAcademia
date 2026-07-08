<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use App\Services\Context\CurrentContext;

/**
 * Policy que usa o CurrentContext para decidir acesso a uma Organization (Tenant).
 * Isso substitui verificações manuais nos Controllers.
 *
 * Uso nos Controllers:
 *   $this->authorize('manage', $organization);
 */
class OrganizationPolicy
{
    protected CurrentContext $context;

    public function __construct(CurrentContext $context)
    {
        $this->context = $context;
    }

    /**
     * Administradores globais podem ver qualquer organização.
     * Usuários comuns só veem as suas.
     */
    public function view(User $user, Organization $organization): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->organizations()
            ->where('organization_id', $organization->id)
            ->wherePivot('is_active', true)
            ->exists();
    }

    /**
     * Apenas admin global ou admin da organização pode gerenciar.
     */
    public function manage(User $user, Organization $organization): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->organizations()
            ->where('organization_id', $organization->id)
            ->wherePivot('role', 'admin')
            ->wherePivot('is_active', true)
            ->exists();
    }

    /**
     * Pode criar uma organização se for professional.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['professional', 'instructor', 'supervisor', 'admin']);
    }

    /**
     * Pode vincular membros se for admin da org ativa no contexto.
     */
    public function attachUser(User $user, Organization $organization): bool
    {
        if ($user->hasRole('admin')) return true;

        // Verifica pelo contexto ativo (mais eficiente — sem nova query)
        if ($this->context->tenant()?->id === $organization->id && $this->context->isClinicAdmin()) {
            return true;
        }

        // Fallback: consulta direta na pivot
        return $user->organizations()
            ->where('organization_id', $organization->id)
            ->wherePivot('role', 'admin')
            ->wherePivot('is_active', true)
            ->exists();
    }
}
