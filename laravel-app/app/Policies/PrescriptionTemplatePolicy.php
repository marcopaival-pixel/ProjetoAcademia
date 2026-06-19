<?php

namespace App\Policies;

use App\Models\PrescriptionTemplate;
use App\Models\User;

class PrescriptionTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isProfessional() || $user->isAdministrator();
    }

    public function view(User $user, PrescriptionTemplate $template): bool
    {
        return $user->isAdministrator() || (int) $template->professional_id === (int) $user->id;
    }

    public function update(User $user, PrescriptionTemplate $template): bool
    {
        return $this->view($user, $template);
    }

    public function delete(User $user, PrescriptionTemplate $template): bool
    {
        return $this->view($user, $template);
    }
}
