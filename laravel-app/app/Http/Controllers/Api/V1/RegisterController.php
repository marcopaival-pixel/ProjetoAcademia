<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuthAuditLog;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\UserConsent;
use App\Models\UserProfile;
use App\Rules\CpfValido;
use App\Services\Operations\AuthAuditService;
use App\Services\StudentRoleBridgeService;
use App\Support\Cpf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function store(
        Request $request,
        AuthAuditService $authAudit,
        StudentRoleBridgeService $studentBridge,
    ): JsonResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'cpf' => ['required', 'string', 'size:11', Rule::unique('users', 'cpf'), new CpfValido()],
            'password' => ['required', 'confirmed', Password::min(8)],
            'account_type' => ['required', 'string', Rule::in(['aluno', 'professional'])],
            'device_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date_format:Y-m-d', 'before:today'],
            'phone' => ['nullable', 'string', 'max:25'],
            'sex' => ['nullable', Rule::in(['M', 'F', 'O'])],
            'height_cm' => ['nullable', 'integer', 'between:50,260'],
            'current_weight_kg' => ['nullable', 'numeric', 'between:20,500'],
            'goal' => ['nullable', Rule::in(['lose', 'lose_aggressive', 'recomp', 'maintain', 'gain', 'performance'])],
            'activity_level' => ['nullable', Rule::in(['sedentary', 'light', 'moderate', 'active', 'very_active'])],
            'has_injury' => ['nullable', 'boolean'],
            'injury_details' => ['nullable', 'string', 'max:1000'],
            'has_disease' => ['nullable', 'boolean'],
            'disease_details' => ['nullable', 'string', 'max:1000'],
            'uses_medication' => ['nullable', 'boolean'],
            'medication_details' => ['nullable', 'string', 'max:1000'],
            'fitness_notes' => ['nullable', 'string', 'max:1500'],
            'accepted_terms' => ['nullable', 'accepted'],
        ]);

        $user = DB::transaction(function () use ($validated, $request, $studentBridge) {
            $role = Role::where('name', $validated['account_type'])->first();
            $freePlan = Plan::where('name', 'Free')->first();
            $emailVerificationEnabled = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);
            $hasOnboardingPayload = filled($validated['birth_date'] ?? null)
                && filled($validated['sex'] ?? null)
                && filled($validated['height_cm'] ?? null)
                && filled($validated['current_weight_kg'] ?? null)
                && filled($validated['goal'] ?? null)
                && filled($validated['activity_level'] ?? null);

            $user = new User();
            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'cpf' => Cpf::normalize($validated['cpf']),
                'phone' => $validated['phone'] ?? null,
                'profile_id' => $role?->id,
                'plan_id' => $freePlan?->id,
                'status' => $emailVerificationEnabled ? 'pending_email_verification' : 'active',
                'onboarding_status' => $hasOnboardingPayload ? 'completed' : 'pending',
                'profile_completion_percentage' => $hasOnboardingPayload ? 100 : 0,
                'registration_approval_status' => 'approved',
                'email_verified' => ! $emailVerificationEnabled,
                'email_verified_at' => $emailVerificationEnabled ? null : now(),
                'email_verification_expires_at' => $emailVerificationEnabled ? now()->addHours(24) : null,
            ]);
            $user->password_hash = Hash::make($validated['password']);
            $user->save();

            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            if ($validated['account_type'] === 'aluno') {
                $studentBridge->ensurePortalAccess($user);
            }

            UserProfile::create([
                'user_id' => $user->id,
                'birth_date' => $validated['birth_date'] ?? null,
                'sex' => $validated['sex'] ?? '',
                'height_cm' => $validated['height_cm'] ?? null,
                'activity_level' => $validated['activity_level'] ?? null,
                'goal' => $validated['goal'] ?? null,
                'has_injury' => $validated['has_injury'] ?? false,
                'injury_details' => $validated['injury_details'] ?? null,
                'has_disease' => $validated['has_disease'] ?? false,
                'disease_details' => $validated['disease_details'] ?? null,
                'uses_medication' => $validated['uses_medication'] ?? false,
                'medication_details' => $validated['medication_details'] ?? null,
                'fitness_notes' => $validated['fitness_notes'] ?? null,
                'profile_completed_at' => $hasOnboardingPayload ? now() : null,
            ]);

            if ($hasOnboardingPayload) {
                $user->weightEntries()->create([
                    'weighed_at' => now()->toDateString(),
                    'weight_kg' => $validated['current_weight_kg'],
                ]);
            }

            UserConsent::create([
                'user_id' => $user->id,
                'consent_type' => 'privacy_policy_and_terms',
                'version' => '1.0',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            return $user->fresh('roles');
        });

        if (! $user->isActive() || ! $user->isEmailVerified()) {
            return response()->json([
                'message' => 'Conta criada com sucesso. Verifique seu e-mail antes de entrar.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                ],
            ], 201);
        }

        $deviceName = $validated['device_name'] ?? 'nexshape-android';
        $expirationDays = (int) config('projeto.api_token_expiration_days', 30);
        $expiresAt = $expirationDays > 0 ? now()->addDays($expirationDays) : null;
        $token = $user->createToken($deviceName, ['*'], $expiresAt);

        $authAudit->log(
            AuthAuditLog::EVENT_API_TOKEN_ISSUED,
            $user->id,
            $user->email,
            true,
            $request,
            ['device_name' => $deviceName, 'source' => 'api_register'],
            'sanctum'
        );

        return response()->json([
            'message' => 'Conta criada com sucesso.',
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'expires_at' => $expiresAt?->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ], 201);
    }
}
