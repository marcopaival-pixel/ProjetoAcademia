<?php

namespace App\Services\Lgpd;

use App\Mail\LgpdDeletionRequestMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LgpdDpoNotificationService
{
    public function notifyDeletionRequest(User $user, ?string $reason = null): bool
    {
        $recipient = trim((string) config('mail.lgpd.dpo_address', ''));

        if ($recipient === '' || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::warning('LGPD DPO email not configured — pedido de exclusão não notificado.', [
                'user_id' => $user->id,
            ]);

            return false;
        }

        try {
            Mail::to($recipient)->send(new LgpdDeletionRequestMail(
                $user,
                $reason,
                route('admin.lgpd.deletion-requests')
            ));

            return true;
        } catch (\Throwable $e) {
            Log::error('Falha ao notificar DPO sobre pedido LGPD.', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
