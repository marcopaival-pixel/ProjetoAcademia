<?php

return [

    /*
    | URLs usadas por monitorização externa (UptimeRobot, Pingdom, etc.).
    | Não substituem health checks reais — servem como referência em runbooks.
    */
    'uptime_url' => env('UPTIME_MONITOR_URL', env('APP_URL', 'http://localhost').'/up'),

    'health_url' => env('HEALTH_MONITOR_URL', env('APP_URL', 'http://localhost').'/health'),

    /*
    | Sentry (opcional — requer pacote sentry/sentry-laravel).
    | Ver docs/MONITORAMENTO.md
    */
    'sentry_dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    'sentry_traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.1),

];
