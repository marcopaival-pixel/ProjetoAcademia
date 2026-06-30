<?php

namespace App\Http\Controllers\Shopping;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\ShopWishlist;
use App\Services\Shop\ShopCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopProductController extends Controller
{
    public function __construct(private ShopCartService $cartService) {}

    public function show(string $slug)
    {
        $product = ShopProduct::published()
            ->where('slug', $slug)
            ->with('images', 'category', 'vendor')
            ->firstOrFail();

        $user = Auth::user();

        $inWishlist = ShopWishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        // Produtos relacionados da mesma categoria
        $related = ShopProduct::published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->take(4)
            ->get();

        $cartSummary = $this->cartService->summary($user);

        return view('shopping.product', compact('product', 'related', 'inWishlist', 'cartSummary'));
    }
}
