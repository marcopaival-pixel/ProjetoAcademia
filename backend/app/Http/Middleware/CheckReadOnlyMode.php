<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Operations\OperationalControlService;
use Symfony\Component\HttpFoundation\Response;

class CheckReadOnlyMode
{
    protected $ops;

    public function __construct(OperationalControlService $ops)
    {
        $this->ops = $ops;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->ops->isReadOnly()) {
            return $next($request);
        }

        // Allow GET, HEAD, OPTIONS
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        // Keep operational controls reachable so admins can safely leave read-only mode.
        if ($this->ops->canBypass($request->user()) && $this->isOperationalControlRequest($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'O sistema está em modo somente leitura para manutenção da base de dados.',
                'read_only' => true
            ], 403);
        }

        return back()->with('error', 'O sistema está em modo somente leitura. Alterações não são permitidas no momento.');
    }

    private function isOperationalControlRequest(Request $request): bool
    {
        return $request->routeIs('admin.operations.update', 'admin.logout')
            || $request->is('admin/operations/update', 'admin/logout');
    }
}
