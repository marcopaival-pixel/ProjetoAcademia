<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Erros fatais (parse, memória, etc.) não passam pelo Laravel — gravamos aqui para diagnóstico.
 * Ver: storage/logs/php-fatal.log
 */
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err === null) {
        return;
    }
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (! in_array($err['type'], $fatalTypes, true)) {
        return;
    }
    $line = sprintf(
        "[%s] type=%s %s in %s:%d\n",
        date('c'),
        $err['type'],
        $err['message'],
        $err['file'],
        $err['line']
    );
    @file_put_contents(__DIR__.'/../storage/logs/php-fatal.log', $line, FILE_APPEND | LOCK_EX);
});

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
