<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserProfileResource;
use App\Models\UserProfile;
use App\Models\WeightEntry;
use App\Services\Nutrition;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Rules\CpfValido;
use App\Support\Cpf;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use FormatsApiResponses;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing(['roles', 'branding', 'organizations']);

        return $this->success((new UserProfileResource($user))->resolve());
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $isPurePatient = $user->hasRole('paciente') && ! $user->hasRole('aluno') && ! $user->isAdministrator();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'birth_date' => ['sometimes', 'date_format:Y-m-d', 'before:today'],
            'sex' => ['sometimes', Rule::in(['M', 'F', 'O'])],
            'height_cm' => ['sometimes', 'nullable', 'integer', 'between:50,260'],
            'current_weight_kg' => ['sometimes', 'nullable', 'numeric', 'between:20,500'],
            'target_weight_kg' => ['sometimes', 'nullable', 'numeric', 'between:20,500'],
            'activity_level' => ['sometimes', Rule::in(['sedentary', 'light', 'moderate', 'active', 'very_active'])],
            'climate' => ['sometimes', Rule::in(['cold', 'moderate', 'hot'])],
            'goal' => ['sometimes', Rule::in(['lose', 'lose_aggressive', 'recomp', 'maintain', 'gain', 'performance'])],
            'daily_calorie_target' => ['sometimes', 'nullable', 'integer', 'between:500,20000'],
            'water_target_ml' => ['sometimes', 'nullable', 'integer', 'between:500,10000'],
            'is_water_target_auto' => ['sometimes', 'boolean'],
        ]);

        DB::transaction(function () use ($user, $validated, $isPurePatient) {
            if (! $isPurePatient && isset($validated['name'])) {
                $user->update(['name' => $validated['name']]);
            }

            if (! $isPurePatient && array_key_exists('current_weight_kg', $validated) && $validated['current_weight_kg'] !== null) {
                WeightEntry::updateOrCreate(
                    ['user_id' => $user->id, 'weighed_at' => now()->toDateString()],
                    ['weight_kg' => $validated['current_weight_kg']]
                );
            }

            $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
            $profileUpdate = [];

            foreach (['birth_date', 'sex', 'height_cm'] as $field) {
                if (array_key_exists($field, $validated)) {
                    $profileUpdate[$field] = $validated[$field];
                }
            }

            if (! $isPurePatient) {
                foreach ([
                    'target_weight_kg',
                    'activity_level',
                    'climate',
                    'goal',
                    'daily_calorie_target',
                    'water_target_ml',
                    'is_water_target_auto',
                ] as $field) {
                    if (array_key_exists($field, $validated)) {
                        $profileUpdate[$field] = $validated[$field];
                    }
                }

                if (($profileUpdate['is_water_target_auto'] ?? false) === true) {
                    $weight = $validated['current_weight_kg'] ?? $user->weightEntries()->orderByDesc('weighed_at')->value('weight_kg');
                    if ($weight !== null && ($validated['birth_date'] ?? $profile->birth_date) && ($validated['sex'] ?? $profile->sex)) {
                        $profileUpdate['water_target_ml'] = Nutrition::calculateWaterTarget(
                            (float) $weight,
                            (string) ($validated['birth_date'] ?? $profile->birth_date),
                            (string) ($validated['sex'] ?? $profile->sex),
                            (string) ($validated['activity_level'] ?? $profile->activity_level ?? 'moderate'),
                            (string) ($validated['climate'] ?? $profile->climate ?? 'moderate')
                        );
                    }
                }
            }

            if ($profileUpdate !== []) {
                $profile->update($profileUpdate);
            }
        });

        return $this->success((new UserProfileResource($user->fresh()->loadMissing(['roles', 'branding', 'organizations'])))->resolve());
    }

    public function completeOnboarding(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'birth_date' => ['required', 'date_format:Y-m-d', 'before:today'],
            'phone' => ['required', 'string', 'max:25'],
            'cpf' => ['required', 'string', 'size:11', Rule::unique('users', 'cpf')->ignore($user->id), new CpfValido()],
            'sex' => ['required', Rule::in(['M', 'F', 'O'])],
            'height_cm' => ['required', 'integer', 'between:50,260'],
            'current_weight_kg' => ['required', 'numeric', 'between:20,500'],
            'goal' => ['required', Rule::in(['lose', 'lose_aggressive', 'recomp', 'maintain', 'gain', 'performance'])],
            'activity_level' => ['required', Rule::in(['sedentary', 'light', 'moderate', 'active', 'very_active'])],
            'has_injury' => ['required', 'boolean'],
            'injury_details' => ['nullable', 'string', 'max:1000'],
            'has_disease' => ['required', 'boolean'],
            'disease_details' => ['nullable', 'string', 'max:1000'],
            'uses_medication' => ['required', 'boolean'],
            'medication_details' => ['nullable', 'string', 'max:1000'],
            'fitness_notes' => ['nullable', 'string', 'max:1500'],
            'accepted_terms' => ['accepted'],
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'phone' => $validated['phone'],
                'cpf' => Cpf::normalize($validated['cpf']),
                'onboarding_status' => 'completed',
                'profile_completion_percentage' => 100,
            ]);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'birth_date' => $validated['birth_date'],
                    'sex' => $validated['sex'],
                    'height_cm' => $validated['height_cm'],
                    'activity_level' => $validated['activity_level'],
                    'goal' => $validated['goal'],
                    'has_injury' => $validated['has_injury'],
                    'injury_details' => $validated['injury_details'] ?? null,
                    'has_disease' => $validated['has_disease'],
                    'disease_details' => $validated['disease_details'] ?? null,
                    'uses_medication' => $validated['uses_medication'],
                    'medication_details' => $validated['medication_details'] ?? null,
                    'fitness_notes' => $validated['fitness_notes'] ?? null,
                    'profile_completed_at' => now(),
                ]
            );

            $user->weightEntries()->create([
                'weighed_at' => now()->toDateString(),
                'weight_kg' => $validated['current_weight_kg'],
            ]);
        });

        return $this->success([
            'message' => 'Onboarding concluido com sucesso.',
            'profile_completion_percentage' => 100,
            'onboarding_status' => 'completed',
        ]);
    }
}
