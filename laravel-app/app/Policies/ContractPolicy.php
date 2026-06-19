<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function view(User $user, Contract $contract): bool
    {
        return $user->isAdministrator()
            || (int) $contract->representative_id === (int) $user->id;
    }
}
