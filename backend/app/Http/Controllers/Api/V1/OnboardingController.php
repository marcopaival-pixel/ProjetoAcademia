<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\WeightEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function storeProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'birth_date' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:30'],
            'cpf' => ['required', 'string', 'max:14'],
            'sex' => ['required', 'string', 'in:M,F,m,f,male,female,Masculino,Feminino'],
            'height_cm' => ['required', 'integer', 'min:50', 'max:250'],
            'current_weight_kg' => ['required', 'numeric', 'min:20', 'max:500'],
            'goal' => ['required', 'string', 'max:100'],
            'activity_level' => ['required', 'string', 'max:50'],
            'has_injury' => ['nullable', 'boolean'],
            'injury_details' => ['nullable', 'string', 'max:2000'],
            'has_disease' => ['nullable', 'boolean'],
            'disease_details' => ['nullable', 'string', 'max:2000'],
            'uses_medication' => ['nullable', 'boolean'],
            'medication_details' => ['nullable', 'string', 'max:2000'],
            'fitness_notes' => ['nullable', 'string', 'max:2000'],
            'accepted_terms' => ['accepted'],
        ]);

        $user = $request->user();
        $sex = strtoupper(substr($validated['sex'], 0, 1));
        if (! in_array($sex, ['M', 'F'], true)) {
            $sex = str_contains(strtolower($validated['sex']), 'f') ? 'F' : 'M';
        }

        $user->update([
            'phone' => $validated['phone'],
            'cpf' => $validated['cpf'],
            'onboarding_status' => 'completed',
        ]);

        $profile = $user->profile()->firstOrNew(['user_id' => $user->id]);
        $profile->fill([
            'birth_date' => $validated['birth_date'],
            'sex' => $sex,
            'height_cm' => $validated['height_cm'],
            'goal' => $this->normalizeGoal($validated['goal']),
            'activity_level' => $validated['activity_level'],
            'has_injury' => (bool) ($validated['has_injury'] ?? false),
            'injury_details' => $validated['injury_details'] ?? null,
            'has_disease' => (bool) ($validated['has_disease'] ?? false),
            'disease_details' => $validated['disease_details'] ?? null,
            'uses_medication' => (bool) ($validated['uses_medication'] ?? false),
            'medication_details' => $validated['medication_details'] ?? null,
            'fitness_notes' => $validated['fitness_notes'] ?? null,
            'profile_completed_at' => now(),
        ]);
        $profile->save();

        WeightEntry::updateOrCreate(
            ['user_id' => $user->id, 'weighed_at' => now()->toDateString()],
            ['weight_kg' => $validated['current_weight_kg']]
        );

        $user->unsetRelation('profile');
        $percentage = method_exists($user, 'updateProfileCompletion')
            ? $user->updateProfileCompletion()
            : 100;

        if ($percentage < 100) {
            $user->update(['profile_completion_percentage' => max($percentage, 90)]);
        }

        return response()->json([
            'data' => [
                'message' => 'Perfil de onboarding concluído.',
                'profile_completion_percentage' => (int) ($user->fresh()->profile_completion_percentage ?? $percentage),
                'onboarding_status' => $user->fresh()->onboarding_status ?? 'completed',
            ],
        ]);
    }

    private function normalizeGoal(string $goal): string
    {
        $map = [
            'lose' => 'lose',
            'lose_weight' => 'lose',
            'emagrecer' => 'lose',
            'gain' => 'gain',
            'gain_mass' => 'gain',
            'hipertrofia' => 'gain',
            'maintain' => 'maintain',
            'manter' => 'maintain',
            'performance' => 'performance',
            'recomp' => 'recomp',
        ];

        $key = strtolower(trim($goal));

        return $map[$key] ?? $key;
    }
}
