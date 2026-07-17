<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Whitelist de rotas acessíveis para usuários Free (Alunos)
        $allowedRoutes = [
            'patient.dashboard',
            'patient.profile',
            'patient.profile.update',
            'patient.reports.index',
            'plano',
            'checkout',
            'payment.*',
            'notifications.*',
            'logout',
            'support.*',
            'help.*',
            'home',
            'profile.edit',
            'profile.update',
        ];

        $user = $request->user();

        // Segurança: Admin e Staff Clínico sempre passam por este middleware
        if ($user && ($user->isAdministrator() || $user->hasRole(['professional', 'manager', 'instructor', 'supervisor', 'receptionist']))) {
            return $next($request);
        }

        if ($request->routeIs($allowedRoutes)) {
            return $next($request);
        }

        if (!$user || !$user->hasPremiumAccess()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Esta funcionalidade é exclusiva para assinantes Premium.'
                ], 403);
            }

            // Se for uma rota de "relatório" específica que já tem seu próprio gating, deixa passar
            // para que o ReportController ou MonthlyReportPdfController lidem com a mensagem amigável.
            if ($request->routeIs(['report.monthly.pdf', 'bioimpedance.pdf', 'patient.report.export'])) {
                return $next($request);
            }

            $subscription = $user ? $user->subscriptions()->latest()->first() : null;
            
            // Redirecionar com mensagem apenas quando há assinatura pendente de confirmação
            if ($subscription && in_array($subscription->status, [\App\Models\Subscription::STATUS_FIN_PENDENTE, \App\Models\Subscription::STATUS_FIN_AGUARDANDO])) {
                return redirect()->route('plano')->with('info', 'Sua assinatura ainda não foi confirmada. O acesso será liberado assim que o pagamento for processado.');
            }

            // Redirect silencioso para /plano — sem session('error') que dispara o modal
            return redirect()->route('plano');
        }

        return $next($request);
    }
}
