<?php

namespace App\Services\Operations;

use Illuminate\Support\Facades\Cache;

class JobMetricsRecorder
{
    private const COMPLETED_KEY = 'operations.jobs_completed_last_hour';

    private const DURATION_KEY = 'operations.jobs_duration_ms_last_hour';

    public static function recordCompleted(int $durationMs = 0): void
    {
        try {
            Cache::add(self::COMPLETED_KEY, 0, now()->addHour());
            Cache::add(self::DURATION_KEY, 0, now()->addHour());
            Cache::increment(self::COMPLETED_KEY);
            if ($durationMs > 0) {
                $current = (int) Cache::get(self::DURATION_KEY, 0);
                Cache::put(self::DURATION_KEY, $current + $durationMs, now()->addHour());
            }
        } catch (\Throwable) {
            // Metrics must not break job processing.
        }
    }

    public static function recordFailed(): void
    {
        // Failed jobs are tracked via failed_jobs table.
    }
}
