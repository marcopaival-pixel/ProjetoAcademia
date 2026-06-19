<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Http\Requests\CouponRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * Lista os cupons solicitados pelo profissional.
     */
    public function index(): View
    {
        $coupons = auth()->user()->requestedCoupons()
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('professional.coupons.index', compact('coupons'));
    }

    /**
     * Exibe o formulário de solicitação de cupom.
     */
    public function create(): View
    {
        $patients = auth()->user()->patients;
        return view('professional.coupons.create', compact('patients'));
    }

    /**
     * Salva uma nova solicitação de cupom.
     */
    public function store(CouponRequest $request)
    {
        $data = $request->validated();
        $data['professional_id'] = auth()->id();
        $data['status'] = 'pending';

        Coupon::create($data);

        return redirect()->route('professional.coupons.index')
            ->with('success', 'Sua solicitação de cupom foi enviada para análise do administrador.');
    }

    /**
     * Visualiza os detalhes de um cupom.
     */
    public function show(Coupon $coupon)
    {
        $this->authorize('view', $coupon);
        return view('professional.coupons.show', compact('coupon'));
    }
}


