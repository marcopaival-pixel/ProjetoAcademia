<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Preços por modelo (USD por 1M tokens)
    |--------------------------------------------------------------------------
    */
    'token_prices' => [
        'gpt-4o' => ['input' => (float) env('OPENAI_PRICE_GPT4O_INPUT', 5.00), 'output' => (float) env('OPENAI_PRICE_GPT4O_OUTPUT', 15.00)],
        'gpt-4o-mini' => ['input' => (float) env('OPENAI_PRICE_GPT4O_MINI_INPUT', 0.15), 'output' => (float) env('OPENAI_PRICE_GPT4O_MINI_OUTPUT', 0.60)],
        'gpt-4-turbo' => ['input' => (float) env('OPENAI_PRICE_GPT4_TURBO_INPUT', 10.00), 'output' => (float) env('OPENAI_PRICE_GPT4_TURBO_OUTPUT', 30.00)],
        'gpt-3.5-turbo' => ['input' => (float) env('OPENAI_PRICE_GPT35_INPUT', 0.50), 'output' => (float) env('OPENAI_PRICE_GPT35_OUTPUT', 1.50)],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache de respostas IA
    |--------------------------------------------------------------------------
    */
    'response_cache_ttl' => (int) env('AI_RESPONSE_CACHE_TTL', 86400),

    /*
    |--------------------------------------------------------------------------
    | Memória conversacional (sliding window)
    |--------------------------------------------------------------------------
    */
    'chat_history_messages' => (int) env('AI_CHAT_HISTORY_MESSAGES', 10),

    /*
    |--------------------------------------------------------------------------
    | Intents desabilitados (stubs — sem LLM)
    |--------------------------------------------------------------------------
    */
    'disabled_intents' => ['finance', 'sales', 'retention'],

    /*
    |--------------------------------------------------------------------------
    | Classificador LLM — só quando keyword router não resolve
    |--------------------------------------------------------------------------
    */
    'llm_classifier_enabled' => (bool) env('AI_LLM_CLASSIFIER_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Fila assíncrona para chamadas IA
    |--------------------------------------------------------------------------
    */
    'queue_enabled' => (bool) env('AI_QUEUE_ENABLED', false),
    'queue_name' => env('QUEUE_NAME_AI', 'ai'),

    /*
    |--------------------------------------------------------------------------
    | Limites de governança e alertas
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'daily_usd_global' => (float) env('AI_DAILY_USD_LIMIT_GLOBAL', 0),
        'daily_usd_per_clinic' => (float) env('AI_DAILY_USD_LIMIT_CLINIC', 0),
        'error_rate_alert_percent' => (float) env('AI_ERROR_RATE_ALERT_PERCENT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature code padrão do orquestrador
    |--------------------------------------------------------------------------
    */
    'default_feature_code' => 'ai_chat',

];
