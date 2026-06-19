<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuthAuditLog;
use App\Models\User;
use App\Services\Operations\AuthAuditService;
use App\Services\StudentRoleBridgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
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
}
