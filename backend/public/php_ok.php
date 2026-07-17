<?php

/**
 * Teste mínimo: não usa Laravel. Se isto falhar, o problema é PHP/servidor (.htaccess, módulos, caminho).
 * Abrir: http://127.0.0.1:8000/php_ok.php (artisan serve) ou .../public/php_ok.php (Apache)
 */
header('Content-Type: text/plain; charset=UTF-8');
echo 'PHP_OK '.PHP_VERSION;
