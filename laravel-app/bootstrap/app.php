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
        $middleware->api(prepend: [
            \App\Http\Middleware\AssignRequestId::class,
            \App\Http\Middleware\LogApiAccess::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\EnsureRegistrationApproved::class,
            \App\Http\Middleware\EnsureEmailIsVerified::class,
            \App\Http\Middleware\EnsurePasswordIsNotForced::class,
            \App\Http\Middleware\TenantMiddleware::class, // Adicionado aqui para contexto global
            \App\Http\Middleware\ProfileCompletionMiddleware::class,
            \App\Http\Middleware\UpdateLastActivity::class,
            \App\Http\Middleware\EnsureHasProfessionalLink::class,
            \App\Http\Middleware\EnsurePanelIsolation::class,
            \App\Http\Middleware\CheckRouteMenuAccess::class,
            \App\Http\Middleware\EnforcePatientReadOnly::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \App\Http\Middleware\CheckReadOnlyMode::class,
            \App\Http\Middleware\HandleClinicImpersonation::class,
            \App\Http\Middleware\RepresentativeTrackingMiddleware::class,
            \App\Http\Middleware\EnsureDemoSafety::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
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
            'active_patient' => \App\Http\Middleware\RequireActivePatient::class,
            'professional.panel' => \App\Http\Middleware\EnsureUserIsProfessional::class,
            'panel.isolation' => \App\Http\Middleware\EnsurePanelIsolation::class,
            'api.tenant' => \App\Http\Middleware\SetApiTenantContext::class,
            'api.role' => \App\Http\Middleware\EnsureApiRole::class,
            'api.active_patient' => \App\Http\Middleware\ResolveApiActivePatient::class,
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

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if (! \App\Support\Api\V1ErrorResponse::isApiV1Request($request)) {
                return null;
            }

            return \App\Support\Api\V1ErrorResponse::make(
                'Dados inválidos.',
                422,
                'validation_error',
                $e->errors()
            );
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if (! \App\Support\Api\V1ErrorResponse::isApiV1Request($request)) {
                return null;
            }

            return \App\Support\Api\V1ErrorResponse::make('Não autenticado.', 401, 'unauthenticated');
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if (! \App\Support\Api\V1ErrorResponse::isApiV1Request($request)) {
                return null;
            }

            return \App\Support\Api\V1ErrorResponse::make('Recurso não encontrado.', 404, 'not_found');
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e) {
            if (app()->runningInConsole()) return null;

            return response()->view('errors.db-down', [], 503);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if (\App\Support\Api\V1ErrorResponse::isApiV1Request($request)) {
                $status = $e->getStatusCode();
                if ($status === 404) {
                    return \App\Support\Api\V1ErrorResponse::make('Recurso não encontrado.', 404, 'not_found');
                }

                return \App\Support\Api\V1ErrorResponse::make(
                    $e->getMessage() ?: 'Erro na requisição.',
                    $status,
                    'http_error'
                );
            }

            if ($e->getStatusCode() === 403 && ! app()->runningInConsole()) {
                \App\Services\AccessDeniedAuditService::log(request(), 403, $e->getMessage() ?: 'http_403');
            }

            if ($e->getStatusCode() == 500) {
                return response()->view('errors.500', [], 500);
            }
            return null;
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if (\App\Support\Api\V1ErrorResponse::isApiV1Request($request)) {
                return \App\Support\Api\V1ErrorResponse::make(
                    $e->getMessage() ?: 'Acesso negado.',
                    403,
                    'forbidden'
                );
            }

            if (! app()->runningInConsole()) {
                \App\Services\AccessDeniedAuditService::log(request(), 403, $e->getMessage() ?: 'authorization');
            }

            return null;
        });
    })->create();
