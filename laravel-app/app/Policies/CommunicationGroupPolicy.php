<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CommunicationGroup;

class CommunicationGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, CommunicationGroup $group): bool
    {
        return $user->is_admin || $group->members()->where('user_id', $user->id)->exists();
    }

    public function manage(User $user, CommunicationGroup $group): bool
    {
        // Administradores e Moderadores do grupo (ou admins do sistema)
        if ($user->is_admin) return true;

        $membership = $group->users()->where('user_id', $user->id)->first();
        
        return $membership && in_array($membership->pivot->role, ['moderator', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, CommunicationGroup $group): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, CommunicationGroup $group): bool
    {
        return $user->is_admin;
    }
}
