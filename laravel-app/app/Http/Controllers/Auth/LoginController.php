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
            /* Verificação suspensa
            if (! $user->email_verified_at) {
                return redirect()->route('verification.notice');
            }
            */

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

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], false)) {
            throw ValidationException::withMessages([
                'email' => 'Usuário ou senha incorretos.',
            ]);
        }

        $user = Auth::user();

        if ($user->isRegistrationRejected()) {
            $request->session()->regenerate();

            return redirect()->route('registration.rejected');
        }

        if ($user->isRegistrationPending()) {
            $request->session()->regenerate();

            return redirect()->route('registration.pending');
        }

        /* Verificação suspensa
        if (! $user->isAdministrator() && ! $user->email_verified_at) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Seu email ainda não foi confirmado. Verifique sua caixa de entrada.',
                ])
                ->with('unverified_email', $request->input('email'));
        }
        */

        $request->session()->regenerate();
        $request->session()->flash('success', 'Acesso autorizado. Bem-vindo de volta!');

        // Se tiver mais de um papel, vai para a seleção
        if ($user->roles->count() > 1) {
            return redirect()->route('profile.selection');
        }

        if ($user->isAdministrator()) {
            return redirect()->intended(route('admin.dashboard'));
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
