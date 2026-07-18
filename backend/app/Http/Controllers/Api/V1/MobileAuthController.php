<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google_Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class MobileAuthController extends Controller
{
    /**
     * Registrar um novo usuário mobile.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->status = 'active'; // Default to active or pending based on business logic
            // Other necessary default fields can go here
            $user->save();

            $user->setPlainPassword($validated['password']);
            
            // Atribuir perfil de aluno por padrão
            $user->assignRole('aluno');

            DB::commit();

            $token = $user->createToken('mobile-auth')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar usuário mobile: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno ao registrar usuário.'], 500);
        }
    }

    /**
     * Autenticação unificada via Google (id_token).
     */
    public function google(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $idToken = $request->id_token;
        $client = new Google_Client();
        
        // Em um cenário real, você listaria os CLIENT_IDs do Android, iOS e Web aqui.
        // O verifyIdToken valida a assinatura criptográfica usando a chave pública do Google.
        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            return response()->json(['message' => 'Token do Google inválido.'], 401);
        }

        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];
        $avatar = $payload['picture'] ?? null;

        $user = User::where('email', $email)->first();

        if ($user) {
            // Atualiza Google ID caso o usuário já exista (ex: criou com senha, e agora loga com Google)
            if (!$user->google_id) {
                $user->google_id = $googleId;
                $user->provider = 'google';
                if ($avatar && !$user->avatar) {
                    $user->avatar = $avatar;
                }
                $user->save();
            }
        } else {
            // Cria um novo usuário
            DB::beginTransaction();
            try {
                $user = new User();
                $user->name = $name;
                $user->email = $email;
                $user->google_id = $googleId;
                $user->provider = 'google';
                $user->avatar = $avatar;
                $user->status = 'active';
                $user->email_verified_at = now(); // Assumimos que e-mails do Google são verificados
                $user->save();

                // Gera senha aleatória
                $user->setPlainPassword(Str::random(24));
                $user->assignRole('aluno');

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erro ao criar usuário via Google: ' . $e->getMessage());
                return response()->json(['message' => 'Erro interno ao registrar usuário via Google.'], 500);
            }
        }

        $token = $user->createToken('mobile-google-auth')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->profile_photo_url,
            ],
        ]);
    }

    /**
     * Solicitar redefinição de senha (envio de link padrão Laravel ou código).
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 400);
    }

    /**
     * Redefinir a senha (consumindo o token gerado pelo forgotPassword).
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->setPlainPassword($password);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
