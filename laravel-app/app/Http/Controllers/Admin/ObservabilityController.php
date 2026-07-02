<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ApiAccessLog;
use App\Models\AuthAuditLog;
use App\Models\ClientErrorLog;
use App\Models\SystemError;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\PaymentReconciliationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ObservabilityController extends Controller
{
    public function dashboard(Request $request): View
    {
        $timeWindow = now()->subHours(24);

        // 1. Usuários Online (ativos nos últimos 15 min) e cadastros de hoje
        $usersOnline = User::where('last_activity_at', '>=', now()->subMinutes(15))->count();
        $registrationsToday = User::where('created_at', '>=', now()->startOfDay())->count();

        // 2. Estatísticas de API (dos logs de acesso nas últimas 24h)
        $totalRequests = 0;
        $successRequests = 0;
        $failedRequests = 0;
        $errorRate = 0;
        $avgDuration = 0;

        if (Schema::hasTable('api_access_logs')) {
            $totalRequests = ApiAccessLog::where('created_at', '>=', $timeWindow)->count();
            if ($totalRequests > 0) {
                $successRequests = ApiAccessLog::where('created_at', '>=', $timeWindow)
                    ->where('status_code', '<', 400)
                    ->count();
                $failedRequests = ApiAccessLog::where('created_at', '>=', $timeWindow)
                    ->where('status_code', '>=', 400)
                    ->count();
                $errorRate = round(($failedRequests / $totalRequests) * 100);
                $avgDuration = round(ApiAccessLog::where('created_at', '>=', $timeWindow)->avg('duration_ms') ?? 0);
            }
        }

        // 3. Exceções e erros (Backend vs Frontend) nas últimas 24h
        $criticalErrors = Schema::hasTable('system_errors') 
            ? SystemError::where('created_at', '>=', $timeWindow)->count() 
            : 0;

        $clientWarnings = Schema::hasTable('client_error_logs') 
            ? ClientErrorLog::where('created_at', '>=', $timeWindow)->count() 
            : 0;

        $lastException = Schema::hasTable('system_errors')
            ? SystemError::orderByDesc('created_at')->first()
            : null;

        // 4. Heatmap de erros (rotas mais problemáticas dos logs de API com status >= 400)
        $errorHeatmap = [];
        if (Schema::hasTable('api_access_logs')) {
            $errorHeatmap = ApiAccessLog::select('path', DB::raw('count(*) as total'))
                ->where('created_at', '>=', now()->subDays(7))
                ->where('status_code', '>=', 400)
                ->groupBy('path')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        // 5. Status de Serviços Externos e Integrados
        $pulseActive = (bool) config('pulse.enabled', true);
        $sentryActive = (bool) config('sentry.dsn');
        $horizonActive = (bool) config('observability.horizon.enabled', false);

        // 6. Últimos erros registrados (Backend)
        $recentErrors = Schema::hasTable('system_errors')
            ? SystemError::with('user')->orderByDesc('created_at')->limit(5)->get()
            : collect();

        // 7. Métricas de Negócio (Cadastros)
        $registrationsStarted = User::count();
        $registrationsCompleted = User::where('profile_completion_percentage', 100)
            ->orWhere('onboarding_status', 'completed')
            ->count();
        $registrationsSuccessRate = $registrationsStarted > 0 
            ? round(($registrationsCompleted / $registrationsStarted) * 100) 
            : 100;

        // 8. Configurações de Alertas
        $slackWebhookConfigured = (bool) config('observability.alerts.slack_webhook_url');
        $whatsappAlertsActive = (bool) config('observability.alerts.whatsapp_enabled', false);
        $alertDedupeMinutes = (int) config('observability.alerts.dedupe_minutes', 30);
        $diskAlertPercent = (int) config('observability.alerts.disk_warning_percent', 85);

        return view('admin.observability.dashboard', compact(
            'usersOnline',
            'registrationsToday',
            'totalRequests',
            'successRequests',
            'failedRequests',
            'errorRate',
            'avgDuration',
            'criticalErrors',
            'clientWarnings',
            'lastException',
            'errorHeatmap',
            'pulseActive',
            'sentryActive',
            'horizonActive',
            'recentErrors',
            'registrationsStarted',
            'registrationsCompleted',
            'registrationsSuccessRate',
            'slackWebhookConfigured',
            'whatsappAlertsActive',
            'alertDedupeMinutes',
            'diskAlertPercent'
        ));
    }

    public function adminLogs(Request $request): View
    {
        $logs = $this->adminLogsQuery($request)->paginate(50)->withQueryString();

        return view('admin.observability.admin-logs', compact('logs'));
    }

    public function exportAdminLogs(Request $request): StreamedResponse
    {
        return $this->streamCsv(
            'admin-logs-'.now()->format('Y-m-d').'.csv',
            ['Data', 'Operador', 'Ação', 'IP'],
            $this->adminLogsQuery($request)->limit(5000)->cursor(),
            fn ($log) => [
                $log->created_at?->format('Y-m-d H:i:s'),
                $log->user?->name ?? 'SISTEMA',
                $log->action,
                $log->ip_address,
            ],
        );
    }

    public function authLogs(Request $request): View
    {
        $logs = $this->authLogsQuery($request)->paginate(50)->withQueryString();

        return view('admin.observability.auth-logs', compact('logs'));
    }

    public function exportAuthLogs(Request $request): StreamedResponse
    {
        return $this->streamCsv(
            'auth-logs-'.now()->format('Y-m-d').'.csv',
            ['Data', 'Evento', 'E-mail', 'Sucesso', 'IP'],
            $this->authLogsQuery($request)->limit(5000)->cursor(),
            fn ($log) => [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->event,
                $log->email ?? $log->user?->email,
                $log->success ? 'sim' : 'não',
                $log->ip,
            ],
        );
    }

    public function apiLogs(Request $request): View
    {
        $logs = $this->apiLogsQuery($request)->paginate(50)->withQueryString();

        return view('admin.observability.api-logs', compact('logs'));
    }

    public function exportApiLogs(Request $request): StreamedResponse
    {
        return $this->streamCsv(
            'api-logs-'.now()->format('Y-m-d').'.csv',
            ['Data', 'Método', 'Path', 'Status', 'ms', 'Usuário'],
            $this->apiLogsQuery($request)->limit(5000)->cursor(),
            fn ($log) => [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->method,
                $log->path,
                $log->status_code,
                $log->duration_ms,
                $log->user?->email,
            ],
        );
    }

    public function clientErrors(Request $request): View
    {
        $logs = $this->clientErrorsQuery($request)->paginate(50)->withQueryString();

        return view('admin.observability.client-errors', compact('logs'));
    }

    public function exportClientErrors(Request $request): StreamedResponse
    {
        return $this->streamCsv(
            'client-errors-'.now()->format('Y-m-d').'.csv',
            ['Data', 'Tipo', 'Mensagem', 'URL'],
            $this->clientErrorsQuery($request)->limit(5000)->cursor(),
            fn ($log) => [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->type,
                $log->message,
                $log->url,
            ],
        );
    }

    private function adminLogsQuery(Request $request)
    {
        $query = AdminLog::with('user')->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->string('action').'%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->string('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->string('date_to'));
        }

        return $query;
    }

    private function authLogsQuery(Request $request)
    {
        $query = AuthAuditLog::with('user')->orderByDesc('created_at');

        if ($request->filled('event')) {
            $query->where('event', $request->string('event'));
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->string('email').'%');
        }
        if ($request->filled('success')) {
            $query->where('success', $request->boolean('success'));
        }

        return $query;
    }

    private function apiLogsQuery(Request $request)
    {
        $query = ApiAccessLog::with('user')->orderByDesc('created_at');

        if ($request->filled('path')) {
            $query->where('path', 'like', '%'.$request->string('path').'%');
        }
        if ($request->filled('status')) {
            $query->where('status_code', $request->integer('status'));
        }

        return $query;
    }

    private function clientErrorsQuery(Request $request)
    {
        $query = ClientErrorLog::with('user')->orderByDesc('created_at');

        if ($request->filled('url')) {
            $query->where('url', 'like', '%'.$request->string('url').'%');
        }

        return $query;
    }

    public function financialReconciliation(PaymentReconciliationService $service): JsonResponse
    {
        return response()->json($service->analyze(7, now()->subDays(30)));
    }

    public function userHistory(Request $request, $userId = null): View
    {
        $user = null;
        $timeline = collect();
        $recentUsers = User::orderByDesc('last_activity_at')->limit(10)->get();

        if ($userId) {
            $user = User::with('academyCompany')->find($userId);
        } elseif ($request->filled('search')) {
            $user = User::with('academyCompany')
                ->where('email', 'like', '%'.$request->string('search').'%')
                ->orWhere('name', 'like', '%'.$request->string('search').'%')
                ->first();
        }

        if ($user) {
            $authLogs = AuthAuditLog::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(30)
                ->get()
                ->map(fn($log) => [
                    'type' => 'auth',
                    'title' => 'Autenticação: ' . $log->event,
                    'details' => $log->success ? 'Conexão realizada com sucesso.' : 'Tentativa de login malsucedida (IP: ' . $log->ip . ')',
                    'date' => $log->created_at,
                    'class' => $log->success ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' : 'text-red-400 bg-red-500/10 border-red-500/20',
                    'icon' => $log->success ? 'fa-sign-in-alt' : 'fa-exclamation-triangle',
                ]);

            $apiLogs = ApiAccessLog::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
                ->map(fn($log) => [
                    'type' => 'api',
                    'title' => 'Requisição HTTP ' . $log->method . ' /' . $log->path,
                    'details' => 'Status: ' . $log->status_code . ' | Duração: ' . $log->duration_ms . ' ms | IP: ' . $log->ip,
                    'date' => $log->created_at,
                    'class' => $log->status_code >= 400 ? 'text-amber-400 bg-amber-500/10 border-amber-500/20' : 'text-zinc-400 bg-zinc-800 border-white/5',
                    'icon' => 'fa-link',
                ]);

            $systemErrors = SystemError::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
                ->map(fn($log) => [
                    'type' => 'error',
                    'title' => 'Erro Crítico (' . $log->type . ')',
                    'details' => $log->message . ' em ' . $log->url,
                    'date' => $log->created_at,
                    'class' => 'text-red-500 bg-red-500/10 border border-red-500/20',
                    'icon' => 'fa-bug',
                ]);

            $timeline = $timeline->concat($authLogs)->concat($apiLogs)->concat($systemErrors)->sortByDesc('date');
        }

        return view('admin.observability.user-history', compact('user', 'timeline', 'recentUsers'));
    }

    public function apiIntegrationsMetrics(): View
    {
        $integrations = collect();
        if (Schema::hasTable('api_integration_logs')) {
            $integrations = DB::table('api_integration_logs')
                ->select(
                    'api_name',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when status_code >= 400 or error_message is not null then 1 else 0 end) as errors'),
                    DB::raw('sum(case when response_time_ms >= 5000 then 1 else 0 end) as timeouts'),
                    DB::raw('round(avg(response_time_ms)) as avg_time')
                )
                ->groupBy('api_name')
                ->get()
                ->map(function ($item) {
                    $item->availability = $item->total > 0 
                        ? round((($item->total - $item->errors) / $item->total) * 100, 2) 
                        : 100;
                    return $item;
                });
        }

        return view('admin.observability.api-integrations', compact('integrations'));
    }

    /**
     * @param  iterable<int, mixed>  $rows
     * @param  callable(mixed): array<int, scalar|null>  $mapRow
     */
    private function streamCsv(string $filename, array $headers, iterable $rows, callable $mapRow): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows, $mapRow) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers, ';');
            foreach ($rows as $row) {
                fputcsv($out, $mapRow($row), ';');
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
