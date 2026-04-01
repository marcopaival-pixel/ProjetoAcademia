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
        $isPremium = $user->isPremiumActive();
        $mpFlash = (string) session()->pull('flash_mp_error', '');
        $mpConfigured = config('projeto.mp_access_token') !== ''
            && rtrim((string) config('projeto.public_url'), '/') !== '';

        return view('plano', [
            'isPremium' => $isPremium,
            'mpFlash' => $mpFlash,
            'mpConfigured' => $mpConfigured,
            'webhookUrl' => $mp->absoluteUrl('mp/webhook'),
        ]);
    }
}
