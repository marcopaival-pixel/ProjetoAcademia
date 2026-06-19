<?php

namespace App\Policies;

use App\Models\ProfessionalFinanceCategory;
use App\Models\User;

class ProfessionalFinanceCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isProfessional() || $user->isAdministrator();
    }

    public function update(User $user, ProfessionalFinanceCategory $category): bool
    {
        return $user->isAdministrator() || (int) $category->professional_id === (int) $user->id;
    }

    public function delete(User $user, ProfessionalFinanceCategory $category): bool
    {
        return $this->update($user, $category);
    }
}
