<?php

return [

    'api_log' => [
        'enabled' => env('API_ACCESS_LOG_ENABLED', true),
        'sample_rate' => (float) env('API_ACCESS_LOG_SAMPLE_RATE', 1.0),
        'ignore_paths' => [
            'up',
            'health',
            'api/v1/health',
            'api/v1/client-errors',
        ],
    ],

    'client_errors' => [
        'enabled' => env('CLIENT_ERROR_LOG_ENABLED', true),
        'rate_limit' => (int) env('CLIENT_ERROR_RATE_LIMIT', 10),
    ],

    'alerts' => [
        'slack_webhook_url' => env('SLACK_OPS_WEBHOOK_URL'),
        'whatsapp_enabled' => env('WHATSAPP_OPS_ENABLED', false),
        'whatsapp_api_url' => env('WHATSAPP_OPS_API_URL'),
        'dedupe_minutes' => (int) env('OPS_ALERT_DEDUPE_MINUTES', 30),
        'disk_warning_percent' => (int) env('OPS_DISK_WARNING_PERCENT', 85),
    ],

    'retention_days' => [
        'admin_logs' => (int) env('LOG_RETENTION_ADMIN_DAYS', 30),
        'log_envio_email' => (int) env('LOG_RETENTION_EMAIL_DAYS', 30),
        'system_errors' => (int) env('LOG_RETENTION_ERRORS_DAYS', 15),
        'api_access_logs' => (int) env('LOG_RETENTION_API_DAYS', 30),
        'auth_audit_logs' => (int) env('LOG_RETENTION_AUTH_DAYS', 90),
        'client_error_logs' => (int) env('LOG_RETENTION_CLIENT_DAYS', 15),
        'pdf_generation_logs' => (int) env('LOG_RETENTION_PDF_DAYS', 30),
        'api_integration_logs' => (int) env('LOG_RETENTION_INTEGRATION_DAYS', 30),
        'financial_logs' => (int) env('LOG_RETENTION_FINANCIAL_DAYS', 365),
        'audit_logs' => (int) env('LOG_RETENTION_AUDIT_DAYS', 180),
        'representative_audits' => (int) env('LOG_RETENTION_REP_AUDIT_DAYS', 365),
        'menu_permission_audit_logs' => (int) env('LOG_RETENTION_MENU_AUDIT_DAYS', 180),
        'pdf_signature_audit_logs' => (int) env('LOG_RETENTION_PDF_SIG_AUDIT_DAYS', 365),
        'admin_clinic_access_logs' => (int) env('LOG_RETENTION_CLINIC_ACCESS_DAYS', 180),
    ],

    'queues' => [
        'default' => env('QUEUE_NAME_DEFAULT', 'default'),
        'pdf' => env('QUEUE_NAME_PDF', 'pdf'),
        'ai' => env('QUEUE_NAME_AI', 'ai'),
        'webhooks' => env('QUEUE_NAME_WEBHOOKS', 'webhooks'),
    ],

    'thresholds' => [
        'failed_jobs_per_hour' => (int) env('OPS_FAILED_JOBS_HOUR_THRESHOLD', 10),
        'pending_jobs' => (int) env('OPS_PENDING_JOBS_THRESHOLD', 100),
    ],

    'horizon' => [
        'enabled' => env('HORIZON_ENABLED', false),
        'path' => env('HORIZON_PATH', 'horizon'),
    ],

];
