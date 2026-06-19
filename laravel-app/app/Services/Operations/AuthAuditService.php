<?php

namespace App\Services\Operations;

use App\Models\AuthAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AuthAuditService
{
    public function log(
        string $event,
        ?int $userId = null,
        ?string $email = null,
        bool $success = true,
        ?Request $request = null,
        array $meta = [],
        string $guard = 'web',
    ): void {
        if (! Schema::hasTable('auth_audit_logs')) {
            return;
        }

        $req = $request ?? request();

        AuthAuditLog::create([
            'user_id' => $userId,
            'email' => $email ? mb_substr($email, 0, 255) : null,
            'event' => $event,
            'guard' => $guard,
            'success' => $success,
            'ip' => $req?->ip(),
            'user_agent' => $req?->userAgent() ? mb_substr((string) $req->userAgent(), 0, 500) : null,
            'meta' => $meta === [] ? null : $meta,
        ]);
    }
}
