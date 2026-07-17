<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Operations\OperationalControlService;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
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
        $settings = $this->ops->getSettings();

        if ($settings['maintenance_mode'] === 'off') {
            return $next($request);
        }

        // Always allow health check
        if ($request->is('health') || $request->is('up')) {
            return $next($request);
        }

        // Check if user is admin and can bypass
        if ($this->ops->canBypass($request->user())) {
            return $next($request);
        }

        // Total maintenance or user is not admin
        if ($settings['maintenance_mode'] === 'total' || ($settings['maintenance_mode'] === 'operable' && !$this->ops->canBypass($request->user()))) {
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $settings['maintenance_message'],
                    'maintenance' => true
                ], 503);
            }

            return response()->view('errors.maintenance', [
                'message' => $settings['maintenance_message']
            ], 503);
        }

        return $next($request);
    }
}
