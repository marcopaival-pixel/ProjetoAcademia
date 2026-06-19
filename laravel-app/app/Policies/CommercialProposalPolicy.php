<?php

namespace App\Policies;

use App\Models\CommercialProposal;
use App\Models\User;

class CommercialProposalPolicy
{
    public function view(User $user, CommercialProposal $proposal): bool
    {
        return $user->isAdministrator()
            || (int) $proposal->representative_id === (int) $user->id;
    }

    public function update(User $user, CommercialProposal $proposal): bool
    {
        return $this->view($user, $proposal);
    }
}
