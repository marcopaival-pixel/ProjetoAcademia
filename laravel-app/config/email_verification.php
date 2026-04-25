<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Expiração do token de confirmação de e-mail
    |--------------------------------------------------------------------------
    |
    | Tempo em horas após o qual o link deixará de ser válido.
    |
    */
    'token_ttl_hours' => (int) env('EMAIL_VERIFICATION_TOKEN_TTL_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Limite de envios do e-mail de confirmação (por utilizador)
    |--------------------------------------------------------------------------
    |
    | Máximo de envios (inclui o primeiro após o cadastro) numa janela de 1 hora.
    |
    */
    'max_sends_per_hour' => (int) env('EMAIL_VERIFICATION_MAX_SENDS_PER_HOUR', 3),

];
