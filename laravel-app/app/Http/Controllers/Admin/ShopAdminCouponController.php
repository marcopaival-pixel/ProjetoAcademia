<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ShopCoupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopAdminCouponController extends Controller
{
    public function index(): View
    {
        $coupons = ShopCoupon::query()
            ->withCount('usages')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.shop.coupons.index', compact('coupons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedCouponData($request);
        $data['code'] = strtoupper($data['code']);
        $data['created_by'] = auth()->id();
        $data['status'] = $request->input('status', 'active');

        if (ShopCoupon::where('code', $data['code'])->exists()) {
            return back()->withInput()->with('error', 'Já existe um cupom com este código.');
        }

        $coupon = ShopCoupon::create($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou cupom shopping: {$coupon->code} (#{$coupon->id})",
            'ip_address' => $request->ip(),
            'payload' => ['coupon_id' => $coupon->id, 'code' => $coupon->code],
        ]);

        return redirect()->route('admin.shop.coupons.index')
            ->with('success', 'Cupom cadastrado com sucesso.');
    }

    public function update(Request $request, ShopCoupon $coupon): RedirectResponse
    {
        $data = $this->validatedCouponData($request, $coupon);
        $data['code'] = strtoupper($data['code']);
        $data['status'] = $request->input('status', $coupon->status);

        if (ShopCoupon::where('code', $data['code'])->where('id', '!=', $coupon->id)->exists()) {
            return back()->withInput()->with('error', 'Já existe outro cupom com este código.');
        }

        $coupon->update($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Atualizou cupom shopping: {$coupon->code} (#{$coupon->id})",
            'ip_address' => $request->ip(),
            'payload' => ['coupon_id' => $coupon->id],
        ]);

        return redirect()->route('admin.shop.coupons.index')
            ->with('success', 'Cupom atualizado com sucesso.');
    }

    public function destroy(ShopCoupon $coupon): RedirectResponse
    {
        if ($coupon->usages()->exists()) {
            return back()->with('error', 'Cupons já utilizados não podem ser excluídos — pause o cupom em vez disso.');
        }

        $code = $coupon->code;
        $id = $coupon->id;
        $coupon->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Excluiu cupom shopping: {$code} (#{$id})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.shop.coupons.index')
            ->with('success', 'Cupom excluído com sucesso.');
    }

    private function validatedCouponData(Request $request, ?ShopCoupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed,free_shipping,product_gift'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'minimum_order_value' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0'],
            'max_uses_total' => ['nullable', 'integer', 'min:1'],
            'max_uses_per_user' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['nullable', 'in:active,paused,expired'],
        ]);

        if ($data['type'] === ShopCoupon::TYPE_PERCENTAGE && empty($data['discount_value'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'discount_value' => 'Informe o percentual de desconto.',
            ]);
        }

        if ($data['type'] === ShopCoupon::TYPE_FIXED && empty($data['discount_value'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'discount_value' => 'Informe o valor fixo de desconto.',
            ]);
        }

        $data['free_shipping'] = $data['type'] === ShopCoupon::TYPE_FREE_SHIPPING;
        $data['applies_to'] = 'all';

        return $data;
    }
}
