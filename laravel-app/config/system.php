<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Access Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains settings for the automatic generation of direct
    | access links to the system, especially for production environments.
    |
    */

    'app_url' => env('APP_URL', 'http://localhost'),
    
    'login_url' => env('SYSTEM_LOGIN_URL', env('APP_URL') . '/login'),

    'onboarding' => [
        'enabled' => env('SYSTEM_ONBOARDING_ENABLED', true),
        'send_email' => env('SYSTEM_SEND_WELCOME_EMAIL', true),
        'send_whatsapp' => env('SYSTEM_SEND_WELCOME_WHATSAPP', false),
    ],

    'qr_code' => [
        'path' => 'qrcodes/access/',
        'disk' => 'public',
    ],
];
