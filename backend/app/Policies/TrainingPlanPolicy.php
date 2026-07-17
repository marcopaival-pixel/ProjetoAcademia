<?php

namespace App\Policies;

use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TrainingPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('portal.access');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrainingPlan $trainingPlan): bool
    {
        if ($user->isAdministrator()) return true;
        if ($user->id === $trainingPlan->user_id) return true;
        if ($user->id === $trainingPlan->creator_id) return true;

        // Verifica se o usuário é o profissional do dono do plano
        return $user->patients()->where('users.id', $trainingPlan->user_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('portal.access');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrainingPlan $trainingPlan): bool
    {
        if ($user->isAdministrator()) return true;
        if ($user->id === $trainingPlan->user_id) return true;
        if ($user->id === $trainingPlan->creator_id) return true;

        return $user->patients()->where('users.id', $trainingPlan->user_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrainingPlan $trainingPlan): bool
    {
        if ($user->isAdministrator()) return true;
        if ($user->id === $trainingPlan->user_id) return true;
        if ($user->id === $trainingPlan->creator_id) return true;

        return $user->patients()->where('users.id', $trainingPlan->user_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrainingPlan $trainingPlan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrainingPlan $trainingPlan): bool
    {
        return false;
    }
}
