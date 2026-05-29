<?php

namespace App\Http\Controllers\MercadoPago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = (string) $request->query('collection_status', '');
        $paymentId = (string) $request->query('payment_id', $request->query('preference_id', ''));

        return view('mp.return', compact('status', 'paymentId'));
    }
}
