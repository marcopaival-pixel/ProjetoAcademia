<?php

namespace App\Models\Traits;

trait HasOnboarding
{
    /**
     * Calcula a porcentagem de completitude do perfil.
     */
    public function updateProfileCompletion(): int
    {
        $profile = $this->profile;
        if (!$profile) {
            $this->profile_completion_percentage = 0;
            $this->save();
            return 0;
        }

        $fields = [
            'birth_date',
            'sex',
            'height_cm',
            'activity_level',
            'goal',
            'target_weight_kg',
            'training_days_per_week',
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($profile->$field)) {
                $filled++;
            }
        }

        // Também checar se tem pelo menos um registro de peso
        if ($this->weightEntries()->exists()) {
            $filled++;
        }
        $totalFields = count($fields) + 1;

        $percentage = (int) (($filled / $totalFields) * 100);
        $this->profile_completion_percentage = $percentage;
        
        if ($percentage >= 100) {
            $this->onboarding_status = 'completed';
        }

        $this->save();
        return $percentage;
    }

    public function isOnboardingPending(): bool
    {
        return $this->onboarding_status === 'pending' && $this->profile_completion_percentage < 100;
    }
}
