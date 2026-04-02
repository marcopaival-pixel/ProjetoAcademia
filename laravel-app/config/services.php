<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | Open Food Facts — API pública de consulta (sem chave). Opção: espelho / outro país.
    | @see https://openfoodfacts.github.io/openfoodfacts-server/api/
    */
    'openfoodfacts' => [
        'base_url' => env('OPENFOODFACTS_BASE_URL', 'https://world.openfoodfacts.org'),
        /** Pedidos por minuto por utilizador (pesquisa + detalhe). Alinhar às boas práticas da API OFF. */
        'max_requests_per_minute' => (int) env('OPENFOODFACTS_MAX_REQUESTS_PER_MINUTE', 30),
        /** Segundos em cache para fichas de produto bem-sucedidas (0 = desligado). */
        'cache_product_ttl_seconds' => (int) env('OPENFOODFACTS_CACHE_PRODUCT_TTL', 3600),
        /** Segundos em cache para pesquisas textuais bem-sucedidas (0 = desligado). */
        'cache_search_ttl_seconds' => (int) env('OPENFOODFACTS_CACHE_SEARCH_TTL', 600),
    ],

];
