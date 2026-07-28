<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeoutController extends Controller
{
    /**
     * Endpoint chamado pelo frontend para avisar o backend de atividade (digitação, cliques).
     * Como o header X-User-Activity: true será enviado nesta requisição pelo axios,
     * o middleware de sessão se encarregará de atualizar o last_activity_at.
     * Portanto, o controller apenas retorna sucesso.
     */
    public function ping(Request $request)
    {
        return response()->json(['status' => 'ok']);
    }

    /**
     * Endpoint chamado pelo botão "Continuar conectado".
     * Atualiza o last_activity_at explicitamente, caso queiramos garantir.
     */
    public function renew(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->last_activity_at = now();
            // Apenas para evitar disparar eventos do Laravel desnecessários no timestamp padrão (updated_at)
            $user->saveQuietly();
        }

        return response()->json(['status' => 'renewed']);
    }
}
