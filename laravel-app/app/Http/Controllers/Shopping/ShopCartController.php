<?php

namespace App\Http\Controllers\Shopping;

use App\Http\Controllers\Controller;
use App\Services\Shop\ShopCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopCartController extends Controller
{
    public function __construct(private ShopCartService $cartService) {}

    public function index()
    {
        $summary = $this->cartService->summary(Auth::user());
        return view('shopping.cart', compact('summary'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:shop_products,id',
            'quantity'   => 'sometimes|integer|min:1|max:99',
        ]);

        try {
            $this->cartService->addItem(
                Auth::user(),
                $request->integer('product_id'),
                $request->integer('quantity', 1)
            );

            return back()->with('success', 'Produto adicionado ao carrinho!');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, int $cartItemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);

        try {
            $this->cartService->updateItem(Auth::user(), $cartItemId, $request->integer('quantity'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Carrinho atualizado.');
    }

    public function remove(int $cartItemId)
    {
        $this->cartService->removeItem(Auth::user(), $cartItemId);
        return back()->with('success', 'Item removido do carrinho.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        try {
            $coupon = $this->cartService->applyCoupon(Auth::user(), $request->input('code'));
            return back()->with('success', "Cupom \"{$coupon->code}\" aplicado com sucesso!");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon(Auth::user());
        return back()->with('success', 'Cupom removido.');
    }
}
