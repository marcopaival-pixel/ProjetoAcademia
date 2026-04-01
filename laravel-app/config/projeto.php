<?php

return [
    /** Igual ao PHP: só APP_PUBLIC_URL (webhook/checkout); não usar APP_URL aqui. */
    'public_url' => rtrim((string) env('APP_PUBLIC_URL', ''), '/'),
    'base_path' => rtrim((string) env('APP_BASE_PATH', ''), '/'),
    'mp_access_token' => (string) env('MP_ACCESS_TOKEN', ''),
];