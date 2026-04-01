<?php

namespace App\Http\Controllers\MercadoPago;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutStartController extends Controller
{
    public function __invoke(Request $request, MercadoPagoService $mp): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'in:monthly,yearly'],
            'checkout' => ['nullable', 'in:once,subscribe'],
        ]);
        $plan = (string) $request->input('plan');
        $checkout = (string) $request->input('checkout', 'once');
        if (! in_array($checkout, ['once', 'subscribe'], true)) {
            $checkout = 'once';
        }

        $token = config('projeto.mp_access_token');
        if ($token === '') {
            session()->flash('flash_mp_error', 'Configure MP_ACCESS_TOKEN no .env (credenciais Mercado Pago).');

            return redirect()->route('plano');
        }

        $user = $request->user();
        $uid = (int) $user->id;
        $email = $user->email;

        if ($checkout === 'subscribe') {
            $go = $mp->createPreapprovalSubscription($token, $uid, $email, $plan);
        } else {
            $go = $mp->createCheckoutPreference($token, $uid, $email, $plan);
        }

        if (! $go['ok']) {
            session()->flash('flash_mp_error', 'Não foi possível iniciar o checkout: '.$go['error']);

            return redirect()->route('plano');
        }

        return redirect()->away($go['init_point']);
    }
}
