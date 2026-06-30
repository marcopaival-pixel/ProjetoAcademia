<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthAuditLog;
use App\Services\Operations\AuthAuditService;
use App\Services\PanelAccessService;
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
                session(['active_role' => 'admin']);

                return redirect()->route('admin.dashboard');
            }
            if ($user->isRegistrationPending()) {
                return redirect()->route('registration.pending');
            }
            if ($user->isRegistrationRejected()) {
                return redirect()->route('registration.rejected');
            }
            $verificacaoAtiva = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);
            if ($verificacaoAtiva && !$user->isEmailVerified() && !$user->isAdministrator() && !$user->hasRole('representative')) {
                return redirect()->route('verification.notice', ['email' => $user->email]);
            }

            $panels = app(PanelAccessService::class);
            session(['active_role' => $panels->resolveActiveRole($user)]);

            return redirect($panels->homeRouteForPanel($panels->currentPanel($user)));
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
            app(AuthAuditService::class)->log(
                AuthAuditLog::EVENT_LOGIN_FAILED,
                null,
                $credentials['email'],
                false,
                $request,
            );

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
        if ($verificacaoAtiva && !$user->isEmailVerified() && !$user->isAdministrator() && !$user->hasRole('representative')) {
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

        if ($user->isAnonymized()) {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Esta conta foi encerrada e os dados pessoais foram anonimizados.');
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
        $request->session()->forget(['active_clinic_id', 'impersonated_clinic_id', 'active_role']);
        $request->session()->flash('success', 'Acesso autorizado. Bem-vindo de volta!');

        app(AuthAuditService::class)->log(
            AuthAuditLog::EVENT_LOGIN_SUCCESS,
            $user->id,
            $user->email,
            true,
            $request,
            ['is_admin' => $user->isAdministrator()],
        );

        \Log::info('Login success for: ' . $user->email . ' | Admin: ' . ($user->isAdministrator() ? 'YES' : 'NO'));

        // Se tiver mais de um papel, vai para a seleção (exceto se for Admin puro que queira ir ao painel)
        if ($user->roles->count() > 1 && !$user->isAdministrator()) {
            return redirect()->route('profile.selection');
        }

        $panels = app(PanelAccessService::class);

        if ($user->isAdministrator()) {
            session(['active_role' => 'admin']);
            $fallback = route('admin.dashboard');
            $target = $panels->sanitizeIntendedUrl(
                $request->session()->pull('url.intended'),
                PanelAccessService::PANEL_ADMIN,
                $fallback,
            );
            \Log::info('Redirecting admin to dashboard: ' . $target);

            return redirect($target);
        }

        if ($user->hasRole('representative')) {
            session(['active_role' => 'representative']);
            $fallback = route('representative.dashboard');
            $target = $panels->sanitizeIntendedUrl(
                $request->session()->pull('url.intended'),
                PanelAccessService::PANEL_REPRESENTATIVE,
                $fallback,
            );

            return redirect($target);
        }

        if ($user->hasRole('professional')) {
            session(['active_role' => 'professional']);
            $fallback = route('professional.dashboard');
            $target = $panels->sanitizeIntendedUrl(
                $request->session()->pull('url.intended'),
                PanelAccessService::PANEL_PROFESSIONAL,
                $fallback,
            );

            return redirect($target);
        }

        if ($user->hasRole('paciente')) {
            session(['active_role' => 'paciente']);
            $professionals = $user->professionals()->wherePivot('status', 'Sim')->get();
            $fallback = $professionals->count() > 1
                ? route('patient.dashboard.choice')
                : route('patient.portal');
            $target = $panels->sanitizeIntendedUrl(
                $request->session()->pull('url.intended'),
                PanelAccessService::PANEL_PATIENT,
                $fallback,
            );

            return redirect($target);
        }

        if ($user->hasRole('aluno')) {
            session(['active_role' => 'aluno']);
            $fallback = route('dashboard');
            $target = $panels->sanitizeIntendedUrl(
                $request->session()->pull('url.intended'),
                PanelAccessService::PANEL_STUDENT,
                $fallback,
            );

            return redirect($target);
        }

        session(['active_role' => 'aluno']);

        return redirect(route('dashboard'));
    }
}
