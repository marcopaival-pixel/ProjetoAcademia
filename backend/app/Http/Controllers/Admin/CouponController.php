<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * Lista todas as solicitações de cupons.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Coupon::class);

        $coupons = Coupon::with(['professional', 'patient'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Aprova uma solicitação de cupom e gera o código.
     */
    public function approve(Request $request, Coupon $coupon)
    {
        $this->authorize('update', $coupon);

        if ($coupon->status !== 'pending') {
            return back()->with('error', 'Este cupom não está em estado pendente.');
        }

        // Gera código único: PROF-[Iniciais]-RANDOM
        $profName = $coupon->professional->name;
        $initials = collect(explode(' ', $profName))->map(fn($n) => Str::upper(substr($n, 0, 1)))->take(2)->implode('');
        
        do {
            $code = 'DESC' . $initials . '-' . Str::upper(Str::random(6));
        } while (Coupon::where('code', $code)->exists());

        $coupon->update([
            'code' => $code,
            'status' => 'active',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', "Cupom aprovado com sucesso! Código gerado: {$code}");
    }

    /**
     * Rejeita uma solicitação de cupom.
     */
    public function reject(Request $request, Coupon $coupon)
    {
        $this->authorize('update', $coupon);

        $request->validate(['admin_notes' => 'required|string']);

        $coupon->update([
            'status' => 'cancelled',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Solicitação de cupom rejeitada.');
    }
}
