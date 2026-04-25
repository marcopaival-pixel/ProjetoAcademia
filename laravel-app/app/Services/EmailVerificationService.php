<?php

namespace App\Services;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Support\MailSendType;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class EmailVerificationService
{
    public const VERIFY_OK = 'ok';

    public const VERIFY_INVALID = 'invalid';

    public const VERIFY_EXPIRED = 'expired';

    public const VERIFY_ALREADY = 'already';

    /**
     * Envia o e-mail de verificação para o usuário (respeita limite por hora).
     */
    public function sendVerificationEmail(User $user): bool
    {
        if ($user->isAdministrator()) {
            $user->email_verified_at = $user->email_verified_at ?? now();
            $user->save();

            return true;
        }

        $max = max(1, (int) config('email_verification.max_sends_per_hour', 3));
        $ttlHours = max(1, (int) config('email_verification.token_ttl_hours', 24));
        $key = 'email-verification-send:'.$user->id;

        $sent = RateLimiter::attempt($key, $max, function () use ($user, $ttlHours) {
            $token = Str::random(64);
            $user->email_verification_token = $token;
            $user->email_verification_expires_at = now()->addHours($ttlHours);
            $user->data_envio_confirmacao = now();
            $user->tentativas_envio = (int) $user->tentativas_envio + 1;
            $user->save();

            $mailOk = app(TransactionalMailService::class)->sendToUser(
                new VerifyEmail($user),
                $user,
                $user->academy_company_id,
                MailSendType::EMAIL_VERIFICATION,
                'Confirme seu email',
                'Verificação de conta',
                [],
                [],
                false
            );

            if ($mailOk) {
                $this->logAction($user, 'verification_email_sent');
            }

            return $mailOk;
        }, 3600);

        return (bool) $sent;
    }

    /**
     * @return array{status: string, user?: User}
     */
    public function verify(string $token): array
    {
        $token = trim($token);
        if ($token === '') {
            $this->logFailedVerification(null, self::VERIFY_INVALID, null);

            return ['status' => self::VERIFY_INVALID];
        }

        $user = User::where('email_verification_token', $token)->first();

        if (! $user) {
            $this->logFailedVerification(null, self::VERIFY_INVALID, $token);

            return ['status' => self::VERIFY_INVALID];
        }

        if ($user->email_verified_at) {
            $this->logAction($user, 'email_verification_link_reused_after_verified');

            return ['status' => self::VERIFY_ALREADY, 'user' => $user];
        }

        if ($user->email_verification_expires_at === null || $user->email_verification_expires_at->isPast()) {
            $this->logFailedVerification($user, self::VERIFY_EXPIRED, $token);

            return ['status' => self::VERIFY_EXPIRED, 'user' => $user];
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->email_verification_expires_at = null;
        $user->save();

        $this->logAction($user, 'email_verified');

        return ['status' => self::VERIFY_OK, 'user' => $user];
    }

    /**
     * Registra a ação nos logs de auditoria.
     */
    private function logAction(User $user, string $action): void
    {
        \App\Models\AdminLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'payload' => ['email' => $user->email],
            'created_at' => now(),
        ]);
    }

    private function logFailedVerification(?User $user, string $reason, ?string $token): void
    {
        $payload = [
            'reason' => $reason,
            'token_prefix' => $token !== null ? Str::limit(hash('sha256', $token), 16, '') : null,
        ];

        \App\Models\AdminLog::create([
            'user_id' => $user?->id,
            'action' => 'email_verification_failed',
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'payload' => $payload,
            'created_at' => now(),
        ]);

        \Illuminate\Support\Facades\Log::info('email_verification_failed', [
            'reason' => $reason,
            'user_id' => $user?->id,
            'ip' => request()->ip(),
        ]);
    }
}
