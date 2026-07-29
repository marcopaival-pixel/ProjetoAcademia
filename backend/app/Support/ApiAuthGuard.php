<?php

namespace App\Support;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ApiAuthGuard
{
    /**
     * Alinha regras do login web (LoginController) com a API mobile.
     *
     * @throws ValidationException
     */
    public static function assertCanAuthenticate(User $user): void
    {
        if ($user->isRegistrationPending()) {
            throw ValidationException::withMessages([
                'email' => ['Cadastro aguardando aprovação.'],
            ]);
        }

        if ($user->isRegistrationRejected()) {
            throw ValidationException::withMessages([
                'email' => ['Cadastro rejeitado.'],
            ]);
        }

        $verificacaoAtiva = SystemSetting::isTrue('verificacao_email_ativa', true);
        if ($verificacaoAtiva && ! $user->isEmailVerified() && ! $user->isAdministrator()) {
            throw ValidationException::withMessages([
                'email' => ['E-mail não verificado.'],
            ]);
        }

        if ($user->status === 'inactive' || $user->status === 'blocked') {
            throw ValidationException::withMessages([
                'email' => ['Conta inativa ou bloqueada.'],
            ]);
        }

        if ($user->hasRole('representative') && $user->status !== 'APROVADO' && ! $user->isAdministrator()) {
            throw ValidationException::withMessages([
                'email' => ['Representante aguardando aprovação.'],
            ]);
        }
    }
}
