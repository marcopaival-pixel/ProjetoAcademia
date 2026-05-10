<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isAdministrator()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isRegistrationPending()) {
                return redirect()->route('registration.pending');
            }
            if ($user->isRegistrationRejected()) {
                return redirect()->route('registration.rejected');
            }
            $verificacaoAtiva = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);
            if ($verificacaoAtiva && !$user->isEmailVerified() && !$user->isAdministrator()) {
                return redirect()->route('verification.notice', ['email' => $user->email]);
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = \App\Models\User::withoutGlobalScopes()->where('email', $credentials['email'])->first();

        \Log::debug('Login attempt for: ' . $credentials['email']);
        if (!$user) {
            \Log::warning('User NOT FOUND in Controller: ' . $credentials['email']);
        } else {
            \Log::debug('User FOUND in Controller. Checking password...');
            $check = \Hash::check($credentials['password'], $user->password_hash);
            \Log::debug('Password check result: ' . ($check ? 'TRUE' : 'FALSE'));
        }

        if (!$user || !\Hash::check($credentials['password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => 'Usuário ou senha incorretos.',
            ]);
        }

        // 0. Verificação de validade de senha temporária (Reset Forçado)
        if ($user->force_password_change && $user->temp_password_expires_at && $user->temp_password_expires_at->isPast()) {
            throw ValidationException::withMessages([
                'email' => 'Esta senha temporária expirou (24h). Solicite um novo reset ao administrador.',
            ]);
        }

        // Se passou pelos filtros de credenciais, logamos o usuário para iniciar a sessão
        Auth::login($user, $request->boolean('remember'));

        $verificacaoAtiva = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);

        // 1. Bloqueio por e-mail não confirmado (se a feature estiver ativa)
        if ($verificacaoAtiva && !$user->isEmailVerified() && !$user->isAdministrator()) {
            // O utilizador fica logado, mas o middleware EnsureEmailIsVerified vai restringir o acesso
            // e o redirecionamento abaixo garante que ele veja a instrução imediatamente.
            return redirect()->route('verification.notice', ['email' => $user->email])
                ->with('error', 'Seu e-mail ainda não foi confirmado. Verifique sua caixa de entrada.');
        }

        // 1.5 Redirecionamento para troca de senha obrigatória
        if ($user->force_password_change) {
            return redirect()->route('password.change.force')
                ->with('warning', 'Sua senha foi resetada pelo administrador. Você deve definir uma nova senha agora.');
        }

        // 2. Bloqueio por status da conta (Bloqueado)
        if ($user->isBlocked() && !$user->isAdministrator()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Sua conta está bloqueada. Entre em contato com o suporte.');
        }

        // 3. Verificação de rejeição de cadastro
        if ($user->isRegistrationRejected()) {
            // Mantemos logado mas o middleware EnsureRegistrationApproved cuidará do resto
            return redirect()->route('registration.rejected');
        }

        // 4. Verificação de cadastro pendente (Administrativo)
        if ($user->isRegistrationPending()) {
            if ($user->isRepresentativePending()) {
                return redirect()->route('representative.pending');
            }
            return redirect()->route('registration.pending');
        }

        // 5. Se estiver inativo por outro motivo (mas não pendente nem bloqueado)
        if (!$user->isActive() && !$user->isAdministrator() && !$user->isPending()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Sua conta não está ativa ou ainda não foi aprovada.');
        }

        // 6. Bloqueio específico para representantes (Status deve ser APROVADO)
        if ($user->hasRole('representative') && $user->status !== 'APROVADO' && !$user->isAdministrator()) {
             // Redireciona para página de pendência ou erro se não estiver aprovado
             if ($user->status === 'REPROVADO') {
                 return redirect()->route('registration.rejected');
             }
             return redirect()->route('representative.pending');
        }

        $request->session()->regenerate();
        $request->session()->flash('success', 'Acesso autorizado. Bem-vindo de volta!');

        \Log::info('Login success for: ' . $user->email . ' | Admin: ' . ($user->isAdministrator() ? 'YES' : 'NO'));

        // Se tiver mais de um papel, vai para a seleção (exceto se for Admin puro que queira ir ao painel)
        if ($user->roles->count() > 1 && !$user->isAdministrator()) {
            return redirect()->route('profile.selection');
        }

        if ($user->isAdministrator()) {
            \Log::info('Redirecting admin to dashboard: ' . route('admin.dashboard'));
            // Usamos redirect() direto se não houver intended real para evitar loops em caminhos de login
            $target = $request->session()->pull('url.intended', route('admin.dashboard'));
            
            // Segurança: se o target for a própria página de login, força o dashboard
            if (str_contains($target, '/login')) {
                $target = route('admin.dashboard');
            }
            
            return redirect($target);
        }

        if ($user->hasRole('professional')) {
            return redirect()->intended(route('professional.dashboard'));
        }
        
        if ($user->hasRole('paciente')) {
            $professionals = $user->professionals()->wherePivot('status', 'Sim')->get();
            
            if ($professionals->count() > 1) {
                return redirect()->intended(route('patient.dashboard.choice'));
            }
            
            return redirect()->intended(route('patient.portal'));
        }

        return redirect()->intended(route('dashboard'));
    }
}
