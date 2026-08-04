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

    'openai' => [
        'api_key' => (string) env('OPENAI_API_KEY', ''),
        'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'model_main' => env('OPENAI_MODEL_MAIN', 'gpt-4o'),
        'model_fast' => env('OPENAI_MODEL_FAST', 'gpt-4o-mini'),
        'model_workout_import' => env('OPENAI_WORKOUT_IMPORT_MODEL', env('OPENAI_MODEL_MAIN', 'gpt-4o')),
    ],

    'ai' => [
        'log_retention_days' => (int) env('AI_LOG_RETENTION_DAYS', 90),
        /** Permite execute-action do NexBot (agendar, criar/ajustar treino, etc.). false = IA consultiva only. */
        'student_module_writes' => filter_var(env('AI_STUDENT_MODULE_WRITES', false), FILTER_VALIDATE_BOOL),
        /** Impede IA de alterar planos com professional_id ou creator_id != user_id. */
        'block_ai_modify_prescribed_plans' => filter_var(env('AI_BLOCK_MODIFY_PRESCRIBED_PLANS', true), FILTER_VALIDATE_BOOL),
    ],

    /*
    | Envio de PDF por WhatsApp (opcional). driver: none | http
    | Para http, defina api_url compatível com o seu gateway (contrato pode variar).
    */
    'whatsapp' => [
        'driver' => env('WHATSAPP_DRIVER', 'none'),
        'api_url' => env('WHATSAPP_API_URL', ''),
        'token' => env('WHATSAPP_TOKEN', ''),
    ],
    
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],

    /*
    | Google Cloud Vision — OCR para importação de treino por foto.
    | @see App\Services\OCR\GoogleVisionOCRService
    */
    'google_vision' => [
        'key' => env('GOOGLE_VISION_API_KEY'),
    ],

    'bug_surgeon' => [
        'agent_token' => env('BUG_SURGEON_AGENT_TOKEN'),
        'queue' => env('BUG_SURGEON_QUEUE', 'default'),
        'context_pack_max_files' => (int) env('BUG_SURGEON_CONTEXT_MAX_FILES', 12),
        'auto_diagnosis' => filter_var(env('BUG_SURGEON_AUTO_DIAGNOSIS', true), FILTER_VALIDATE_BOOL),
        'auto_submit_for_approval' => filter_var(env('BUG_SURGEON_AUTO_SUBMIT', true), FILTER_VALIDATE_BOOL),
        'min_confidence' => (int) env('BUG_SURGEON_MIN_CONFIDENCE', 80),
        'system_user_id' => env('BUG_SURGEON_SYSTEM_USER_ID'),
        'ai_model' => env('BUG_SURGEON_AI_MODEL'),
    ],

];
