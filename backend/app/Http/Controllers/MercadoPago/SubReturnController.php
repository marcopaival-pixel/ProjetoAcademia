<?php

namespace App\Http\Controllers\MercadoPago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubReturnController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = (string) $request->query('preapproval_status', $request->query('status', ''));
        $preId = (string) $request->query('preapproval_id', $request->query('collection_id', ''));

        return view('mp.sub-return', compact('status', 'preId'));
    }
}
