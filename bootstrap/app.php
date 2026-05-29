<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
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
            'payment/webhook/*',
            'logout',
            'admin/logout',
            'omnichannel/webhook',
        ]);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->web(append: [
            \App\Http\Middleware\EnsureRegistrationApproved::class,
            \App\Http\Middleware\EnsureEmailIsVerified::class,
            \App\Http\Middleware\EnsurePasswordIsNotForced::class,
            \App\Http\Middleware\TenantMiddleware::class, // Adicionado aqui para contexto global
            \App\Http\Middleware\ProfileCompletionMiddleware::class,
            \App\Http\Middleware\UpdateLastActivity::class,
            \App\Http\Middleware\EnsureHasProfessionalLink::class,
            \App\Http\Middleware\CheckRouteMenuAccess::class,
            \App\Http\Middleware\EnforcePatientReadOnly::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \App\Http\Middleware\CheckReadOnlyMode::class,
            \App\Http\Middleware\HandleClinicImpersonation::class,
            \App\Http\Middleware\RepresentativeTrackingMiddleware::class,
            \App\Http\Middleware\EnsureDemoSafety::class,
        ]);
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdministrator::class,
            'premium' => \App\Http\Middleware\CheckPremiumAccess::class,
            'onboarding' => \App\Http\Middleware\ProfileCompletionMiddleware::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'plan_access' => \App\Http\Middleware\CheckUserPlanAccess::class,
            'pro_patient_limit' => \App\Http\Middleware\CheckProfessionalPatientLimit::class,
            'patient_linked' => \App\Http\Middleware\EnsurePatientLinked::class,
            'menu.access' => \App\Http\Middleware\CheckRouteMenuAccess::class,
            'block.demo.prod' => \App\Http\Middleware\BlockDemoInProduction::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            // Artisan, Tinker, filas: sem Request HTTP válido; evita poluir system_errors e logs com erros de CLI (ex.: PsySH).
            if (app()->runningInConsole()) {
                return;
            }

            \Illuminate\Support\Facades\Log::debug('Capturador de erros invocado: ' . get_class($e));

            // Com a BD em baixo, SystemError::create() também falha — regista só no ficheiro.
            if ($e instanceof \Illuminate\Database\QueryException) {
                \Illuminate\Support\Facades\Log::error('[sql] '.$e->getMessage());
                app(\App\Services\Operations\OperationalAlertService::class)->sendEmergencyEmail(
                    'Urgente: banco de dados indisponível',
                    'Falha de conexão/consulta ao banco detectada durante uma requisição HTTP.',
                    [
                        'url' => request()->fullUrl(),
                        'method' => request()->method(),
                        'error' => $e->getMessage(),
                    ]
                );

                return;
            }

            try {
                $type = 'system';
                if ($e instanceof \Illuminate\Validation\ValidationException) $type = 'validation';
                if ($e instanceof \Illuminate\Auth\AuthenticationException) $type = 'auth';
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() == 403) $type = 'permission';
                
                // Ignorar 404 para não poluir o banco desnecessariamente (opcional, mas recomendado)
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) return;

                \App\Models\SystemError::create([
                    'user_id' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null,
                    'type' => $type,
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'message' => $e->getMessage(),
                    'stack_trace' => substr($e->getTraceAsString(), 0, 10000), // Limite de caracteres para segurança
                    'payload' => request()->except(['password', 'password_confirmation', '_token']),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                if ($type === 'system') {
                    app(\App\Services\Operations\OperationalAlertService::class)->sendEmergencyEmail(
                        'Urgente: erro interno no sistema',
                        'Erro interno detectado durante uma requisição HTTP.',
                        [
                            'url' => request()->fullUrl(),
                            'method' => request()->method(),
                            'error' => $e->getMessage(),
                        ]
                    );
                }
            } catch (\Throwable $loggingError) {
                // Log de emergência caso a gravação no Banco falhe
                file_put_contents(storage_path('logs/system_logging_failure.log'), 
                    date('Y-m-d H:i:s') . ' - Erro ao gravar log: ' . $loggingError->getMessage() . PHP_EOL, 
                    FILE_APPEND);
            }
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e) {
            if (app()->runningInConsole()) return null;

            return response()->view('errors.db-down', [], 503);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($e->getStatusCode() == 500) {
                return response()->view('errors.500', [], 500);
            }
            return null;
        });
    })->create();
