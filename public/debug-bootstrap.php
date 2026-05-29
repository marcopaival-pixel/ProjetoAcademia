<?php

/**
 * Diagnóstico: carrega Composer + bootstrap Laravel. Apenas localhost.
 * Remover ou proteger em produção.
 */
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if ($ip !== '127.0.0.1' && $ip !== '::1') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Forbidden';

    exit;
}

header('Content-Type: text/plain; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    require __DIR__.'/../vendor/autoload.php';
    echo "1) Composer autoload OK\n";

    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo '2) bootstrap/app.php OK ('.get_class($app).")\n";

    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo "3) Application bootstrapped OK\n";

    echo "4) APP_URL would be: ".config('app.url')."\n";
} catch (\Throwable $e) {
    http_response_code(500);
    echo 'FAIL: '.get_class($e).': '.$e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n\n".$e->getTraceAsString();
}
