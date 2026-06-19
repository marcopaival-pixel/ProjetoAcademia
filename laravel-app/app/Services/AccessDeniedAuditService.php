<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AccessDeniedAuditService
{
    private const SENSITIVE_PREFIXES = [
        'professional/',
        'patient/',
        'body-analysis',
        'report/',
        'progression/',
        'assessments',
        'agenda/',
        'api/ai/',
        'secure-files/',
        'export/',
        'privacy/',
    ];

    public static function log(Request $request, int $statusCode, string $reason = 'access_denied'): void
    {
        if ($statusCode !== 403) {
            return;
        }

        $path = ltrim($request->path(), '/');
        if (! self::isSensitivePath($path)) {
            return;
        }

        $userId = Auth::id();
        $context = [
            'path' => mb_substr($path, 0, 500),
            'method' => $request->method(),
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_id' => $userId,
        ];

        Log::warning('Access denied', $context);

        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        try {
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'access_denied',
                'entity_type' => 'http_request',
                'entity_id' => null,
                'new_values' => $context,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent() ? mb_substr((string) $request->userAgent(), 0, 500) : null,
            ]);
        } catch (\Throwable) {
            // Não interromper o fluxo de resposta 403.
        }
    }

    private static function isSensitivePath(string $path): bool
    {
        foreach (self::SENSITIVE_PREFIXES as $prefix) {
            if ($path === rtrim($prefix, '/') || str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
