<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: [
            \App\Support\Theme::COOKIE,
        ]);
        $middleware->validateCsrfTokens(except: [
            'mp/webhook',
            'mp_webhook.php',
            'logout',
            'admin/logout',
        ]);
        $middleware->append(\App\Http\Middleware\HandleCors::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdministrator::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Capturador de erros invocado: ' . get_class($e));
            try {
                $type = 'system';
                if ($e instanceof \Illuminate\Database\QueryException) $type = 'sql';
                if ($e instanceof \Illuminate\Validation\ValidationException) $type = 'validation';
                if ($e instanceof \Illuminate\Auth\AuthenticationException) $type = 'auth';
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() == 403) $type = 'permission';
                
                // Ignorar 404 para não poluir o banco desnecessariamente (opcional, mas recomendado)
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) return;

                \App\Models\SystemError::create([
                    'user_id' => auth()->check() ? auth()->id() : null,
                    'type' => $type,
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'message' => $e->getMessage(),
                    'stack_trace' => substr($e->getTraceAsString(), 0, 10000), // Limite de caracteres para segurança
                    'payload' => request()->except(['password', 'password_confirmation', '_token']),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Throwable $loggingError) {
                // Log de emergência caso a gravação no Banco falhe
                file_put_contents(storage_path('logs/system_logging_failure.log'), 
                    date('Y-m-d H:i:s') . ' - Erro ao gravar log: ' . $loggingError->getMessage() . PHP_EOL, 
                    FILE_APPEND);
            }
        });
    })->create();
