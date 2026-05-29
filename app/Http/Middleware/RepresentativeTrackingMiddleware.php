<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class RepresentativeTrackingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se há um parâmetro de referência (ref ou rep)
        $ref = $request->query('ref') ?: $request->query('rep');

        if ($ref) {
            // Tentar encontrar o representante pelo username ou professional_code
            $representative = User::where('username', $ref)
                ->orWhere('professional_code', $ref)
                ->orWhere('id', is_numeric($ref) ? $ref : 0)
                ->where('is_representative', true)
                ->first();

            if ($representative) {
                // Armazenar o ID do representante na sessão e em um cookie (30 dias)
                session(['representative_id' => $representative->id]);
                
                // Cookie de longa duração para garantir o vínculo mesmo que a sessão expire
                return $next($request)->withCookie(cookie('representative_id', $representative->id, 60 * 24 * 30));
            }
        }

        return $next($request);
    }
}
