<?php
declare(strict_types=1);

/**
 * Função para registrar erros no banco de dados sem interromper o fluxo.
 */
function log_system_error(
    string $tipo,
    string $mensagem,
    ?string $arquivo = null,
    ?int $linha = null
): void {
    global $config;

    try {
        // Garantir que temos uma conexão PDO
        // Usamos require_once para garantir que db.php esteja disponível se chamado de forma isolada
        require_once __DIR__ . '/db.php';
        $pdo = db($config);

        $stmt = $pdo->prepare("
            INSERT INTO logs_erros (tipo_erro, mensagem, arquivo, linha, usuario_id, ip, url, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pendente')
        ");

        $usuario_id = current_user_id();
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $url = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://" . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');

        $stmt->execute([
            $tipo,
            $mensagem,
            $arquivo,
            $linha,
            $usuario_id,
            $ip,
            $url
        ]);
    } catch (Throwable $e) {
        // Fallback: Grava em arquivo se o banco de dados falhar
        $logDir = dirname(__DIR__) . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/system_errors.log';
        $errorMsg = sprintf(
            "[%s] %s: %s em %s:%d (User: %s, IP: %s, URL: %s). ERRO LOG DB: %s\n",
            date('Y-m-d H:i:s'),
            $tipo,
            $mensagem,
            $arquivo ?? '?',
            $linha ?? 0,
            current_user_id() ?? 'anon',
            $_SERVER['REMOTE_ADDR'] ?? '?',
            $_SERVER['REQUEST_URI'] ?? '?',
            $e->getMessage()
        );
        @file_put_contents($logFile, $errorMsg, FILE_APPEND);
    }
}

/**
 * Handler para erros nativos do PHP.
 */
function handle_php_error(int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $tipo = match ($errno) {
        E_ERROR             => 'PHP Fatal Error',
        E_WARNING           => 'PHP Warning',
        E_PARSE             => 'PHP Parse Error',
        E_NOTICE            => 'PHP Notice',
        E_CORE_ERROR        => 'PHP Core Error',
        E_CORE_WARNING      => 'PHP Core Warning',
        E_COMPILE_ERROR     => 'PHP Compile Error',
        E_COMPILE_WARNING   => 'PHP Compile Warning',
        E_USER_ERROR        => 'PHP User Error',
        E_USER_WARNING      => 'PHP User Warning',
        E_USER_NOTICE       => 'PHP User Notice',
        E_STRICT            => 'PHP Strict',
        E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
        E_DEPRECATED        => 'PHP Deprecated',
        E_USER_DEPRECATED   => 'PHP User Deprecated',
        default             => 'PHP Unknown Error',
    };

    log_system_error($tipo, $errstr, $errfile, $errline);

    // Retorna false para permitir que o logger padrão do PHP também funcione
    return false;
}

/**
 * Handler para exceções não capturadas.
 */
function handle_exception(Throwable $exception): void {
    log_system_error(
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );
}

/**
 * Handler para erros que ocorrem no encerramento do script (ex: fatais).
 */
function handle_shutdown(): void {
    $error = error_get_last();
    $fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
    
    if ($error !== null && in_array($error['type'], $fatalErrors)) {
        log_system_error(
            'Fatal Error (Shutdown)',
            $error['message'],
            $error['file'],
            $error['line']
        );
    }
}
