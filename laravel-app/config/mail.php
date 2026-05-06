<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cópias opcionais (CC / BCC) em envios transacionais
    |--------------------------------------------------------------------------
    |
    | Endereços adicionados automaticamente a Mail::send via TransactionalMailService.
    | MAIL_COPY_BCC pode ser uma lista separada por vírgulas.
    |
    */
    'transactional_copy' => [
        'cc' => array_values(array_filter([
            env('MAIL_COPY_ADMIN'),
            env('MAIL_COPY_FINANCE'),
            env('MAIL_COPY_SUPPORT'),
        ], fn ($v) => is_string($v) && $v !== '' && filter_var(trim($v), FILTER_VALIDATE_EMAIL) ? trim($v) : false)),

        'bcc' => array_values(array_filter(array_map(
            'trim',
            array_filter(explode(',', (string) env('MAIL_COPY_BCC', '')))
        ), fn ($e) => $e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerta operacional emergencial
    |--------------------------------------------------------------------------
    |
    | Usado por falhas críticas quando a base de dados pode estar indisponível.
    |
    */
    'operational_alert' => [
        'address' => env('OPERATIONAL_ALERT_EMAIL', env('ADMIN_EMAIL')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Proteção contra envio duplicado (mesmo utilizador / tipo / assunto)
    |--------------------------------------------------------------------------
    */
    'dedupe_seconds' => (int) env('MAIL_DEDUPE_SECONDS', 60),

];
