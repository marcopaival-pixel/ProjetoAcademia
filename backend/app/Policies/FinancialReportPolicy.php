<?php

namespace App\Policies;

use App\Models\User;

class FinancialReportPolicy
{
    public function viewDashboard(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function viewManagement(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function viewReports(User $user): bool
    {
        return $user->isAdministrator();
    }
}
