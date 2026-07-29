<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiAuthGuard;
use App\Support\ApiTokenIssuer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        ApiAuthGuard::assertCanAuthenticate($user);

        $tokenName = $validated['device_name'] ?? 'api-v1';

        return response()->json(
            ApiTokenIssuer::response($user, $tokenName)
        );
    }

    public function refresh(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        ApiAuthGuard::assertCanAuthenticate($user);

        $request->user()->currentAccessToken()?->delete();

        $tokenName = $validated['device_name'] ?? 'api-v1';

        return response()->json(
            ApiTokenIssuer::response($user, $tokenName)
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Token revogado.']);
    }
}
