<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureDemoSafety
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $isDemoMode = session('is_demo_mode', false);

        // 1. Se estiver em modo demo na sessão, mas o usuário não for demo no banco, forçar logout/reset
        if ($isDemoMode && $user && !$user->is_demo) {
            Auth::logout();
            session()->forget('is_demo_mode');
            return redirect()->route('login')->with('error', 'Sessão de demonstração inválida.');
        }

        // 2. Se o usuário for demo, garantir que ele não tente acessar rotas sensíveis (ex: faturamento real, configurações de admin real)
        if ($user && $user->is_demo) {
            // Lista de rotas proibidas para demo
            $forbiddenRoutes = [
                'admin.settings.save',
                'checkout.process',
                'profile.update', // Depende se queremos deixar ele "simular" o update
            ];

            if (in_array($request->route()->getName(), $forbiddenRoutes)) {
                return redirect()->back()->with('warning', 'Esta ação é simulada no modo demonstração e não pode ser persistida.');
            }
        }

        return $next($request);
    }
}
