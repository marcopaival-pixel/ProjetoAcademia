<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanoController extends Controller
{
    public function __invoke(Request $request, MercadoPagoService $mp): View
    {
        $user = $request->user();
        $isPremium = $user ? $user->isPremiumActive() : false;
        $isAdministrator = $user ? $user->isAdministrator() : false;
        $mpFlash = (string) session()->pull('flash_mp_error', '');
        $mpConfigured = config('projeto.mp_access_token') !== ''
            && rtrim((string) config('projeto.public_url'), '/') !== '';

        $plans = \App\Models\Plan::where('is_active', true)
            ->with('planFeatures')
            ->get()
            ->groupBy('type');

        return view('plano', [
            'isPremium' => $isPremium,
            'isAdministrator' => $isAdministrator,
            'mpFlash' => $mpFlash,
            'mpConfigured' => $mpConfigured,
            'webhookUrl' => $mp->absoluteUrl('mp/webhook'),
            'plans' => $plans,
            'user' => $user,
            'pagamentoAtivo' => \App\Models\AdminSetting::isTrue('pagamento_ativo', true),
        ]);
    }
}
