<?php

namespace App\Services\Operations;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OperationalAlertService
{
    public function sendEmergencyEmail(string $title, string $message, array $context = [], int $dedupeMinutes = 30): bool
    {
        $recipient = trim((string) config('mail.operational_alert.address'));
        if ($recipient === '' || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Operational alert email not configured.', ['title' => $title]);

            return false;
        }

        $dedupeKey = 'operational_alert_email_'.md5($title.'|'.$message);
        if (! $this->reserveAlertSlot($dedupeKey, $dedupeMinutes)) {
            return false;
        }

        try {
            Mail::raw($this->buildPlainTextMessage($message, $context), function ($mail) use ($recipient, $title) {
                $mail->to($recipient)->subject('[NexShape] '.$title);
            });

            Log::warning('Operational emergency alert sent.', [
                'recipient' => $recipient,
                'title' => $title,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to send operational emergency alert.', [
                'title' => $title,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function reserveAlertSlot(string $key, int $minutes): bool
    {
        try {
            if (Cache::has($key)) {
                return false;
            }

            Cache::put($key, true, now()->addMinutes($minutes));

            return true;
        } catch (\Throwable) {
            return $this->reserveFileAlertSlot($key, $minutes);
        }
    }

    private function reserveFileAlertSlot(string $key, int $minutes): bool
    {
        $directory = storage_path('framework/operational-alerts');
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $path = $directory.'/'.$key.'.lock';
        if (file_exists($path) && ((int) filemtime($path)) > now()->subMinutes($minutes)->timestamp) {
            return false;
        }

        file_put_contents($path, (string) now()->timestamp, LOCK_EX);

        return true;
    }

    private function buildPlainTextMessage(string $message, array $context): string
    {
        $lines = [
            $message,
            '',
            'Data/hora: '.now()->format('d/m/Y H:i:s'),
        ];

        if ($context !== []) {
            $lines[] = '';
            $lines[] = 'Contexto:';
            $lines[] = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return implode(PHP_EOL, $lines);
    }
}
