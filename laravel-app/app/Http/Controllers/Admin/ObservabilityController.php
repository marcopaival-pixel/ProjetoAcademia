<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ApiAccessLog;
use App\Models\AuthAuditLog;
use App\Models\ClientErrorLog;
use App\Services\PaymentReconciliationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ObservabilityController extends Controller
{
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

    /**
     * @param  iterable<int, mixed>  $rows
     * @param  callable(mixed): array<int, scalar|null>  $mapRow
     */
    public function financialReconciliation(PaymentReconciliationService $service): JsonResponse
    {
        return response()->json($service->analyze(7, now()->subDays(30)));
    }

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
