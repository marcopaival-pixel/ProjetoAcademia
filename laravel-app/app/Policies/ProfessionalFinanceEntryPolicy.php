<?php

namespace App\Policies;

use App\Models\ProfessionalFinanceEntry;
use App\Models\User;

class ProfessionalFinanceEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isProfessional() || $user->isAdministrator();
    }

    public function create(User $user): bool
    {
        return $user->isProfessional() || $user->isAdministrator();
    }

    public function update(User $user, ProfessionalFinanceEntry $entry): bool
    {
        return $user->isAdministrator() || (int) $entry->professional_id === (int) $user->id;
    }

    public function delete(User $user, ProfessionalFinanceEntry $entry): bool
    {
        return $this->update($user, $entry);
    }
}
