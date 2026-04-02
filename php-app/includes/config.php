<?php
declare(strict_types=1);

/**
 * Copie para .env.php (não versionado) ou defina variáveis de ambiente.
 */
$envFile = dirname(__DIR__) . '/.env.php';
if (is_readable($envFile)) {
    /** @var array<string, string|int>|false $loaded */
    $loaded = include $envFile;
    if (is_array($loaded)) {
        foreach ($loaded as $k => $v) {
            if (getenv($k) === false && is_string($k)) {
                putenv("$k=" . (string) $v);
            }
        }
    }
}

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'projetoacademia',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_path' => rtrim((string) (getenv('APP_BASE_PATH') ?: ''), '/'),
        'session_name' => 'paem_sid',  // Simplificado: sem underscores problemáticos
        /** URL pública do site até a pasta public (ex.: https://dominio.com ou https://dominio.com/php-app/public) — obrigatório para MP. */
        'public_url' => rtrim((string) (getenv('APP_PUBLIC_URL') ?: ''), '/'),
    ],
    'mercadopago' => [
        'access_token' => (string) (getenv('MP_ACCESS_TOKEN') ?: ''),
    ],
];
