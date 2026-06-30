<?php

namespace App\Http\Controllers\Shopping;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\ShopWishlist;
use Illuminate\Support\Facades\Auth;

class ShopWishlistController extends Controller
{
    public function index()
    {
        $items = ShopWishlist::where('user_id', Auth::id())
            ->with('product.images')
            ->orderByDesc('created_at')
            ->get();

        return view('shopping.wishlist', compact('items'));
    }

    public function toggle(int $productId)
    {
        ShopProduct::published()->findOrFail($productId);

        $userId  = Auth::id();
        $existing = ShopWishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Produto removido da lista de desejos.';
            $inWishlist = false;
        } else {
            ShopWishlist::create(['user_id' => $userId, 'product_id' => $productId]);
            $message = 'Produto adicionado à lista de desejos!';
            $inWishlist = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['in_wishlist' => $inWishlist, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
