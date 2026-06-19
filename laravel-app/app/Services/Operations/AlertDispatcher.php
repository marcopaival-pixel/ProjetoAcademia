<?php

namespace App\Services\Operations;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlertDispatcher
{
    public function __construct(
        protected OperationalAlertService $emailAlerts,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function dispatchCritical(
        string $title,
        string $message,
        array $context = [],
        ?string $dedupeKey = null,
    ): void {
        $dedupeKey ??= 'ops_alert_'.md5($title.'|'.$message);
        $minutes = (int) config('observability.alerts.dedupe_minutes', 30);

        if (! $this->reserveSlot($dedupeKey, $minutes)) {
            return;
        }

        $this->emailAlerts->sendEmergencyEmail($title, $message, $context, $minutes);
        $this->sendSlack($title, $message, $context);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function sendSlack(string $title, string $message, array $context = []): bool
    {
        $webhook = trim((string) config('observability.alerts.slack_webhook_url'));
        if ($webhook === '') {
            return false;
        }

        $lines = ["*{$title}*", $message];
        if ($context !== []) {
            $lines[] = '```'.json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).'```';
        }

        try {
            $response = Http::timeout(5)->post($webhook, [
                'text' => implode("\n", $lines),
            ]);

            if (! $response->successful()) {
                Log::warning('Slack ops alert failed.', ['status' => $response->status()]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Slack ops alert exception: '.$e->getMessage());

            return false;
        }
    }

    public function sendWhatsApp(?string $phone, string $message): bool
    {
        if (! config('observability.alerts.whatsapp_enabled', false)) {
            return false;
        }

        if (! $phone) {
            return false;
        }

        $apiUrl = trim((string) config('observability.alerts.whatsapp_api_url'));
        if ($apiUrl === '') {
            Log::info("WhatsApp alert skipped (no API URL): {$phone}");

            return false;
        }

        try {
            Http::timeout(5)->post($apiUrl, [
                'number' => $phone,
                'text' => $message,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp ops alert failed: '.$e->getMessage());

            return false;
        }
    }

    protected function reserveSlot(string $key, int $minutes): bool
    {
        try {
            if (Cache::has($key)) {
                return false;
            }

            Cache::put($key, true, now()->addMinutes($minutes));

            return true;
        } catch (\Throwable) {
            return true;
        }
    }
}
