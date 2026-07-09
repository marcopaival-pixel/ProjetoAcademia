<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuthAuditLog;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\UserConsent;
use App\Models\UserProfile;
use App\Services\Operations\AuthAuditService;
use App\Services\StudentRoleBridgeService;
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
            'password' => ['required', 'confirmed', Password::min(8)],
            'account_type' => ['required', 'string', Rule::in(['aluno', 'professional'])],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($validated, $request, $studentBridge) {
            $role = Role::where('name', $validated['account_type'])->first();
            $freePlan = Plan::where('name', 'Free')->first();
            $emailVerificationEnabled = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);

            $user = new User();
            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'profile_id' => $role?->id,
                'plan_id' => $freePlan?->id,
                'status' => $emailVerificationEnabled ? 'pending_email_verification' : 'active',
                'onboarding_status' => 'pending',
                'profile_completion_percentage' => 0,
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
                'birth_date' => null,
                'sex' => '',
            ]);

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
