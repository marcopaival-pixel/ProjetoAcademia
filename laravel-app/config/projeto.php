<?php

return [
    /** Igual ao PHP: só APP_PUBLIC_URL (webhook/checkout); não usar APP_URL aqui. */
    'public_url' => rtrim((string) env('APP_PUBLIC_URL', ''), '/'),
    'base_path' => rtrim((string) env('APP_BASE_PATH', ''), '/'),
    'mp_access_token' => (string) env('MP_ACCESS_TOKEN', ''),
    /**
     * Mensagens do utilizador (role user) por dia no chat IA para contas não Premium.
     * Premium: sem limite por este contador.
     */
    'chat_free_daily_user_messages' => max(1, (int) env('CHAT_FREE_DAILY_USER_MESSAGES', 8)),
];