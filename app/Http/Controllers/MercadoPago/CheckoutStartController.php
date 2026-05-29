<?php

namespace App\Http\Controllers\MercadoPago;

use App\Http\Controllers\Controller;
use App\Contracts\PaymentGatewayInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutStartController extends Controller
{
    public function __invoke(Request $request, PaymentGatewayInterface $gateway): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'in:monthly,yearly'],
            'checkout' => ['nullable', 'in:once,subscribe'],
            'coupon' => ['nullable', 'string', 'max:20'],
        ]);
        $plan = (string) $request->input('plan');
        $checkout = (string) $request->input('checkout', 'once');
        $couponCode = $request->input('coupon');

        if (! in_array($checkout, ['once', 'subscribe'], true)) {
            $checkout = 'once';
        }

        $user = $request->user();
        $uid = (int) $user->id;

        // Check coupon
        $coupon = null;
        if ($couponCode) {
            $coupon = \App\Models\Coupon::where('code', $couponCode)
                ->where('status', 'active')
                ->first();
            
            if (! $coupon || ! $coupon->isValidForUser($uid)) {
                session()->flash('flash_mp_error', 'Cupom inválido ou expirado.');
                return redirect()->route('plano');
            }
        }

        if ($checkout === 'subscribe') {
            $go = $gateway->createSubscription($user, $plan, ['coupon' => $coupon]);
        } else {
            $go = $gateway->createCheckout($user, 0, ['plan' => $plan, 'coupon' => $coupon]);
        }

        if (! $go['ok']) {
            session()->flash('flash_mp_error', 'Não foi possível iniciar o checkout: '.$go['error']);
            return redirect()->route('plano');
        }

        return redirect()->away($go['init_point']);
    }
}
