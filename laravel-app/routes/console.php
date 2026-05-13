<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('system:monitor')->everyMinute();

// Database Backups
Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at('02:00');

// AI Credit Renewal
Schedule::command('ai:credits-renew')->daily()->at('00:01');

// User Cleanup
Schedule::command('app:clean-unverified-users')->hourly();

// Log Maintenance (Manter banco leve - Prioridade 3 da Auditoria)
Schedule::command('app:purge-old-logs --days=15 --force')->dailyAt('03:00');

// System Health Heartbeat
Schedule::command('pulse:check')->everyMinute();
Schedule::command('pulse:work')->everyMinute(); // Garante que o Pulse processe dados se não houver worker dedicado
