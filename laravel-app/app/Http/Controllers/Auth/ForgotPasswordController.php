<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuthAuditLog;
use App\Services\Operations\AuthAuditService;
use App\Services\TransactionalMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email'),
            function (User $user, string $token) {
                $err = app(TransactionalMailService::class)->validateUserForOutgoingMail($user);
                if ($err !== null) {
                    Log::warning('password_reset_blocked', [
                        'user_id' => $user->id,
                        'reason' => $err,
                    ]);

                    return 'blocked:'.$err;
                }
                $user->sendPasswordResetNotification($token);

                return null;
            }
        );

        if (is_string($status) && str_starts_with($status, 'blocked:')) {
            return back()->withErrors(['email' => substr($status, 8)]);
        }

        if ($status === Password::RESET_LINK_SENT) {
            Log::info('Solicitação de recuperação de senha enviada (destino: users.email)', [
                'email_requested' => $request->email,
            ]);

            app(AuthAuditService::class)->log(
                AuthAuditLog::EVENT_PASSWORD_RESET_REQUEST,
                User::where('email', $request->email)->value('id'),
                $request->email,
                true,
                $request
            );

            return back()->with('status', __($status));
        }

        app(AuthAuditService::class)->log(
            AuthAuditLog::EVENT_PASSWORD_RESET_REQUEST,
            null,
            $request->email,
            false,
            $request,
            ['status' => $status]
        );

        return back()->withErrors(['email' => __($status)]);
    }
}
