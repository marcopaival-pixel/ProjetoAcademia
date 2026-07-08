<?php

namespace App\Services\Context;

use App\Models\Organization;
use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CurrentContext
{
    protected ?string $activeRole = null;
    protected ?Organization $tenant = null;
    protected ?User $user = null;

    /**
     * Define o contexto a partir dos headers e do usuário logado.
     * Deve ser chamado pelo ActiveRoleMiddleware.
     */
    public function setContext(?string $role, ?Organization $tenant): void
    {
        $this->activeRole = $role;
        $this->tenant = $tenant;
        $this->user = Auth::user();
    }

    public function activeRole(): ?string
    {
        return $this->activeRole;
    }

    public function tenant(): ?Organization
    {
        return $this->tenant;
    }

    public function user(): ?User
    {
        return $this->user ?: Auth::user();
    }

    public function isAthlete(): bool
    {
        return $this->activeRole === 'paciente' || $this->activeRole === 'athlete';
    }

    public function isProfessional(): bool
    {
        return $this->activeRole === 'professional';
    }

    public function isClinicAdmin(): bool
    {
        return $this->activeRole === 'admin';
    }

    public function professionalProfile(): ?ProfessionalProfile
    {
        $user = $this->user();
        if (!$user) return null;
        /** @var ProfessionalProfile|null $profile */
        $profile = $user->professionalProfile;

        return $profile;
    }
}
