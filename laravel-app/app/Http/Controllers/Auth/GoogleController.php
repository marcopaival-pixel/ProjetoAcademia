<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\AuthAuditLog;
use App\Services\Operations\AuthAuditService;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redireciona o usuário para a página de autenticação do Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtém as informações do usuário do Google e realiza o login/cadastro.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Falha na autenticação com Google.');
        }

        // 1. Verificar se o usuário já existe pelo google_id ou pelo e-mail
        $user = User::where('google_id', $googleUser->id)
                    ->orWhere('email', $googleUser->email)
                    ->first();

        $isNewUser = false;

        try {
            DB::beginTransaction();

            if ($user) {
                // 2. Login Automático / Vinculação de conta existente
                // Se o usuário foi encontrado pelo e-mail mas não tem google_id, vinculamos agora
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->provider = 'google';
                }
                
                // Atualizar avatar se disponível
                if ($googleUser->avatar) {
                    $user->avatar = $googleUser->avatar;
                }
                
                $user->save();
            } else {
                $isNewUser = true;

                // 3. Cadastro Automático
                // Criar um username único baseado no nome
                $username = Str::slug($googleUser->name) . '.' . Str::random(4);
                
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'provider' => 'google',
                    'avatar' => $googleUser->avatar,
                    'username' => $username,
                    'status' => 'active',
                    'onboarding_status' => 'pending',
                    'profile_completion_percentage' => 0,
                    'email_verified_at' => now(), // Google já validou o e-mail
                    'password_hash' => Hash::make(Str::random(24)), // Senha aleatória forte
                ]);

                // Atribuir papel padrão (Aluno)
                // O usuário solicitou Aluno, Profissional ou Paciente. 
                // Por padrão, novos cadastros sociais são Alunos até que seja definido o perfil.
                $role = Role::where('name', 'aluno')->first();
                if ($role) {
                    $user->roles()->sync([$role->id]);
                    $user->profile_id = $role->id; // Compatibilidade legada
                    $user->save();
                }

                // Criar perfil básico
                UserProfile::create([
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();

            Auth::login($user);

            app(AuthAuditService::class)->log(
                $isNewUser ? AuthAuditLog::EVENT_OAUTH_REGISTER : AuthAuditLog::EVENT_OAUTH_LOGIN,
                $user->id,
                $user->email,
                true,
                request(),
                ['provider' => 'google']
            );

            // Redirecionamento baseado no papel
            if ($user->hasRole('professional')) {
                return redirect()->route('professional.dashboard');
            }

            return redirect()->route('dashboard');

        } catch (Exception $e) {
            DB::rollBack();

            app(AuthAuditService::class)->log(
                AuthAuditLog::EVENT_OAUTH_LOGIN,
                null,
                $googleUser->email ?? null,
                false,
                request(),
                ['provider' => 'google', 'error' => $e->getMessage()]
            );

            return redirect()->route('login')->with('error', 'Erro ao processar login social: ' . $e->getMessage());
        }
    }
}
