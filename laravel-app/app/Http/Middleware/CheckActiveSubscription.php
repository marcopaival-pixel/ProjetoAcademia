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

        if (!$user || !$user->hasPremiumAccess()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sua assinatura ainda não foi confirmada ou está inativa.'
                ], 403);
            }

            // Verificar se existe uma assinatura pendente para mostrar mensagem personalizada
            $subscription = $user ? $user->subscriptions()->latest()->first() : null;
            $message = 'Esta funcionalidade está disponível apenas para usuários com assinatura ativa.';
            
            if ($subscription) {
                if ($subscription->status === \App\Models\Subscription::STATUS_FIN_PENDENTE || $subscription->status === \App\Models\Subscription::STATUS_FIN_AGUARDANDO) {
                    $message = 'Sua assinatura ainda não foi confirmada pelo gateway de pagamento. Assim que recebermos a confirmação, seu acesso será liberado automaticamente.';
                } elseif ($subscription->status === \App\Models\Subscription::STATUS_FIN_RECUSADO) {
                    $message = 'Seu último pagamento foi recusado. Por favor, verifique seus dados de pagamento ou tente outro cartão.';
                }
            }

            return redirect()->route('plano')->with('warning', $message);
        }

        return $next($request);
    }
}
