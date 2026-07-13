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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    public function google(Request $request, AuthAuditService $authAudit, StudentRoleBridgeService $studentBridge): JsonResponse
    {
        $validated = $request->validate([
            'id_token' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $googlePayload = $this->verifyGoogleIdToken($validated['id_token']);
        $email = $googlePayload['email'] ?? null;

        if (! $email || empty($googlePayload['email_verified'])) {
            throw ValidationException::withMessages([
                'email' => ['E-mail Google nao verificado.'],
            ]);
        }

        $user = DB::transaction(function () use ($googlePayload, $studentBridge): User {
            $googleId = (string) $googlePayload['sub'];
            $email = (string) $googlePayload['email'];

            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if ($user) {
                $user->forceFill([
                    'google_id' => $user->google_id ?: $googleId,
                    'provider' => 'google',
                    'avatar' => $googlePayload['picture'] ?? $user->avatar,
                    'email_verified' => true,
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ])->save();

                return $user->fresh('roles');
            }

            $role = Role::where('name', 'aluno')->first();
            $freePlan = Plan::where('name', 'Free')->first();

            $user = new User();
            $user->fill([
                'name' => $googlePayload['name'] ?? Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleId,
                'provider' => 'google',
                'avatar' => $googlePayload['picture'] ?? null,
                'profile_id' => $role?->id,
                'plan_id' => $freePlan?->id,
                'status' => 'active',
                'onboarding_status' => 'pending',
                'profile_completion_percentage' => 0,
                'registration_approval_status' => 'approved',
                'email_verified' => true,
                'email_verified_at' => now(),
            ]);
            $user->password_hash = Hash::make(Str::random(48));
            $user->save();

            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            $studentBridge->ensurePortalAccess($user);

            UserProfile::firstOrCreate(['user_id' => $user->id]);

            UserConsent::firstOrCreate([
                'user_id' => $user->id,
                'consent_type' => 'privacy_policy_and_terms',
            ], [
                'version' => '1.0',
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);

            return $user->fresh('roles');
        });

        if ($user->status === 'inactive' || $user->status === 'blocked' || $user->isRegistrationRejected()) {
            throw ValidationException::withMessages([
                'email' => ['Conta inativa, bloqueada ou rejeitada.'],
            ]);
        }

        if ($user->isRegistrationPending()) {
            throw ValidationException::withMessages([
                'email' => ['Cadastro pendente de aprovacao.'],
            ]);
        }

        $studentBridge->ensurePortalAccess($user);

        $authAudit->log(
            $user->wasRecentlyCreated ? AuthAuditLog::EVENT_OAUTH_REGISTER : AuthAuditLog::EVENT_OAUTH_LOGIN,
            $user->id,
            $user->email,
            true,
            $request,
            ['provider' => 'google', 'source' => 'android'],
            'sanctum'
        );

        return response()->json($this->issueTokenResponse($user, $validated['device_name'] ?? 'nexshape-android-google', $request, $authAudit));
    }

    public function store(Request $request, AuthAuditService $authAudit, StudentRoleBridgeService $studentBridge): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->getAuthPassword())) {
            $authAudit->log(
                AuthAuditLog::EVENT_API_TOKEN_FAILED,
                null,
                $validated['email'],
                false,
                $request,
                [],
                'sanctum'
            );

            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        if ($user->status === 'inactive' || $user->status === 'blocked') {
            $authAudit->log(
                AuthAuditLog::EVENT_API_TOKEN_FAILED,
                $user->id,
                $user->email,
                false,
                $request,
                ['reason' => 'account_inactive'],
                'sanctum'
            );

            throw ValidationException::withMessages([
                'email' => ['Conta inativa ou bloqueada.'],
            ]);
        }

        if ($user->force_password_change) {
            throw ValidationException::withMessages([
                'email' => ['É necessário alterar a senha antes de usar a API.'],
            ]);
        }

        if ($user->isRegistrationRejected()) {
            throw ValidationException::withMessages([
                'email' => ['Cadastro rejeitado.'],
            ]);
        }

        if ($user->isRegistrationPending()) {
            throw ValidationException::withMessages([
                'email' => ['Cadastro pendente de aprovação.'],
            ]);
        }

        $verificacaoAtiva = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);
        if ($verificacaoAtiva && ! $user->isEmailVerified() && ! $user->isAdministrator() && ! $user->hasRole('representative')) {
            throw ValidationException::withMessages([
                'email' => ['E-mail não verificado.'],
            ]);
        }

        if ($user->hasRole('representative') && $user->status !== 'APROVADO' && ! $user->isAdministrator()) {
            throw ValidationException::withMessages([
                'email' => ['Representante aguardando aprovação.'],
            ]);
        }

        $studentBridge->ensurePortalAccess($user);

        return response()->json($this->issueTokenResponse($user, $validated['device_name'] ?? 'api-v1', $request, $authAudit));
    }

    public function refresh(Request $request, AuthAuditService $authAudit): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $deviceName = $validated['device_name'] ?? 'api-v1';
        $request->user()->currentAccessToken()?->delete();

        return response()->json($this->issueTokenResponse(
            $user,
            $deviceName,
            $request,
            $authAudit
        ));
    }

    public function destroy(Request $request, AuthAuditService $authAudit): JsonResponse
    {
        $user = $request->user();
        $request->user()->currentAccessToken()?->delete();

        $authAudit->log(
            AuthAuditLog::EVENT_API_TOKEN_REVOKED,
            $user?->id,
            $user?->email,
            true,
            $request,
            [],
            'sanctum'
        );

        return response()->json(['data' => ['revoked' => true]]);
    }

    /**
     * @return array<string, mixed>
     */
    private function issueTokenResponse(User $user, string $tokenName, Request $request, AuthAuditService $authAudit): array
    {
        $expirationDays = (int) config('projeto.api_token_expiration_days', 30);
        $expiresAt = $expirationDays > 0 ? now()->addDays($expirationDays) : null;
        $token = $user->createToken($tokenName, ['*'], $expiresAt);

        $authAudit->log(
            AuthAuditLog::EVENT_API_TOKEN_ISSUED,
            $user->id,
            $user->email,
            true,
            $request,
            ['device_name' => $tokenName],
            'sanctum'
        );

        return [
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'expires_at' => $expiresAt?->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function verifyGoogleIdToken(string $idToken): array
    {
        $response = Http::timeout(8)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'email' => ['Token Google invalido.'],
            ]);
        }

        $payload = $response->json();
        $allowedAudiences = array_filter([
            config('services.google.client_id'),
            config('services.google.android_client_id'),
        ]);

        if (! $allowedAudiences) {
            throw ValidationException::withMessages([
                'email' => ['Login Google nao configurado no servidor.'],
            ]);
        }

        if (! in_array($payload['aud'] ?? null, $allowedAudiences, true)) {
            throw ValidationException::withMessages([
                'email' => ['Aplicativo Google nao autorizado.'],
            ]);
        }

        return $payload;
    }
}
