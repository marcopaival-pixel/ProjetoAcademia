<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! TenantContext::get()) {
            $clinicId = $user->clinic_id;
            if ($clinicId) {
                TenantContext::set((int) $clinicId);
            }
        }

        return $next($request);
    }
}
