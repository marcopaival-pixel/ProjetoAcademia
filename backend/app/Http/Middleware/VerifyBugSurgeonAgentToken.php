<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyBugSurgeonAgentToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.bug_surgeon.agent_token');

        if (! is_string($expected) || $expected === '') {
            return response()->json([
                'message' => 'BUG_SURGEON_AGENT_TOKEN não configurado no servidor.',
            ], 503);
        }

        $provided = $request->header('X-Bug-Surgeon-Token')
            ?? $request->bearerToken();

        if (! is_string($provided) || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Token do agente inválido.'], 401);
        }

        return $next($request);
    }
}
