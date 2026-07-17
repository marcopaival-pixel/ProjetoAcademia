<?php

namespace App\Policies;

use App\Models\DeployRelease;
use App\Models\User;

class DeployReleasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function update(User $user, DeployRelease $deployRelease): bool
    {
        return $user->isAdministrator();
    }
}
