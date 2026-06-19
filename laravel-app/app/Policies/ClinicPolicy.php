<?php

namespace App\Policies;

use App\Models\Clinic;
use App\Models\User;

class ClinicPolicy
{
    public function view(User $user, Clinic $clinic): bool
    {
        return $user->isAdministrator()
            || (int) $clinic->representative_id === (int) $user->id;
    }
}
