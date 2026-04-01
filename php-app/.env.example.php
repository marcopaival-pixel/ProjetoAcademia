<?php
/**
 * Copie para .env.php na raiz de php-app/ e ajuste.
 * Não commite .env.php
 */
return [
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_NAME' => 'academia',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    // '' se o app estiver na raiz do servidor; '/academia/php-app/public' se em subpasta
    'APP_BASE_PATH' => '',
    // URL absoluta até public, sem barra final (ex.: https://seusite.com/php-app/public)
    'APP_PUBLIC_URL' => 'https://localhost',
    // Credenciais em developers.mercadopago.com.br — use token de TESTE primeiro
    'MP_ACCESS_TOKEN' => '',
];
