<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function __construct(
        protected EmailVerificationService $verificationService
    ) {}

    /**
     * Exibe a página informando que o e-mail precisa ser confirmado.
     */
    public function show()
    {
        return Auth::user()->isEmailVerified()
            ? redirect()->route('dashboard')
            : view('auth.verify-email');
    }

    /**
     * Processa a verificação do e-mail através do token (acesso público; o link pode ser aberto sem sessão).
     */
    public function verify(Request $request, string $token)
    {
        $result = $this->verificationService->verify($token);

        if (in_array($result['status'], [EmailVerificationService::VERIFY_OK, EmailVerificationService::VERIFY_ALREADY], true)) {
            $user = $result['user'];
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('email-verification.success');
        }

        if ($result['status'] === EmailVerificationService::VERIFY_EXPIRED) {
            return redirect()->route('email-verification.failed', ['motivo' => 'expirado']);
        }

        return redirect()->route('email-verification.failed', ['motivo' => 'invalido']);
    }

    /**
     * Página de sucesso após confirmação (utilizador autenticado).
     */
    public function success()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return view('auth.email-verification-success');
    }

    /**
     * Token inválido ou expirado (público).
     */
    public function failed(Request $request)
    {
        $motivo = $request->query('motivo', 'invalido');

        return view('auth.email-verification-failed', [
            'motivo' => in_array($motivo, ['expirado', 'invalido'], true) ? $motivo : 'invalido',
        ]);
    }

    /**
     * Reenvia o e-mail de confirmação (utilizador autenticado).
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if ($user->isEmailVerified()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('dashboard'),
                ]);
            }

            return redirect()->route('dashboard');
        }

        if (! $this->verificationService->sendVerificationEmail($user)) {
            $message = 'Limite de reenvios atingido (máximo '.config('email_verification.max_sends_per_hour', 3).' por hora). Tente novamente mais tarde.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 429);
            }

            return back()->with('error', $message);
        }

        $message = 'Um novo link de confirmação foi enviado para o seu e-mail.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Reenvio para quem não tem sessão (ex.: login bloqueado por e-mail não confirmado).
     */
    public function resendGuest(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        $generic = 'Se existir uma conta pendente de confirmação para este e-mail, enviámos um novo link.';

        if (! $user || $user->isAdministrator() || $user->isEmailVerified()) {
            return back()->withInput($request->only('email'))->with('status', $generic);
        }

        if (! $this->verificationService->sendVerificationEmail($user)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Limite de reenvios atingido. Tente novamente em até uma hora.']);
        }

        return back()->withInput($request->only('email'))->with('status', $generic);
    }
}
