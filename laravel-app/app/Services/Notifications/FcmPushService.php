<?php

namespace App\Services\Notifications;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmPushService
{
    public function isConfigured(): bool
    {
        return filled(config('projeto.fcm_server_key'));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): int
    {
        if (! $this->isConfigured()) {
            return 0;
        }

        $tokens = DeviceToken::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('token');

        $sent = 0;
        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $title, $body, $data)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key='.config('projeto.fcm_server_key'),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ]);

            if (! $response->successful()) {
                Log::warning('FCM push failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            $result = $response->json();
            if (($result['failure'] ?? 0) > 0) {
                $this->deactivateInvalidToken($token, $result);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('FCM push exception', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function deactivateInvalidToken(string $token, array $result): void
    {
        $errors = $result['results'] ?? [];
        foreach ($errors as $error) {
            $code = $error['error'] ?? '';
            if (in_array($code, ['InvalidRegistration', 'NotRegistered'], true)) {
                DeviceToken::where('token', $token)->update(['is_active' => false]);
            }
        }
    }
}
