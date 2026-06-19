<?php

return [
    /** Igual ao PHP: só APP_PUBLIC_URL (webhook/checkout); não usar APP_URL aqui. */
    'public_url' => rtrim((string) env('APP_PUBLIC_URL', ''), '/'),
    'base_path' => rtrim((string) env('APP_BASE_PATH', ''), '/'),
    'mp_access_token' => (string) env('MP_ACCESS_TOKEN', ''),

    /**
     * Segredo para validação HMAC-SHA256 dos webhooks do Mercado Pago.
     * Definir em produção em: Sua conta MP > Webhooks > Chave secreta.
     * Se vazio, a validação de assinatura é ignorada (dev/local apenas).
     */
    'mp_webhook_secret' => (string) env('MP_WEBHOOK_SECRET', ''),
    /**
     * Mensagens do utilizador (role user) por dia no chat IA para contas não Premium.
     * Premium: sem limite por este contador.
     */
    'chat_free_daily_user_messages' => max(1, (int) env('CHAT_FREE_DAILY_USER_MESSAGES', 8)),

    /**
     * Se não vazio, POST /omni/webhook deve enviar o header X-Omni-Secret com o mesmo valor
     * (integrações servidor-servidor). Em desenvolvimento pode ficar vazio.
     */
    'omni_webhook_secret' => (string) env('OMNI_WEBHOOK_SECRET', ''),

    /**
     * Preços dos planos Premium (SaaS).
     * Valores em BRL (Float).
     */
    'prices' => [
        'monthly' => (float) env('PRICE_PREMIUM_MONTHLY', 29.9),
        'yearly'  => (float) env('PRICE_PREMIUM_YEARLY', 299.0),
    ],

    /** Escrita na tabela legada mercadopago_payment_credits (desligado por padrão). */
    'legacy_mp_credits_write' => (bool) env('LEGACY_MP_CREDITS_WRITE', false),

    /** Expiração de tokens Sanctum emitidos via API v1 (dias). 0 = sem expiração. */
    'api_token_expiration_days' => max(0, (int) env('API_TOKEN_EXPIRATION_DAYS', 30)),

    /** Firebase Cloud Messaging — server key para push notifications mobile. */
    'fcm_server_key' => (string) env('FCM_SERVER_KEY', ''),
];