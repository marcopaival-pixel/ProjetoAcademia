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
             ->with(['images', 'category', 'vendor', 'reviews' => function($q) {
                 $q->approved()->with('user')->latest();
             }])
             ->firstOrFail();

        $user = Auth::user();

        $inWishlist = ($user && $user->academy_company_id) ? ShopWishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists() : false;

        // Produtos relacionados da mesma categoria
        $related = ShopProduct::published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->take(4)
            ->get();

        $cartSummary = ($user && $user->academy_company_id) ? $this->cartService->summary($user) : [
            'cart' => null,
            'subtotal' => 0.0,
            'discount' => 0.0,
            'shipping' => 0.0,
            'total' => 0.0,
            'coupon' => null,
        ];

        return view('shopping.product', compact('product', 'related', 'inWishlist', 'cartSummary'));
    }

    public function storeReview(Request $request, ShopProduct $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $product->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'is_approved' => true, // Auto-aprovado em ambiente local/simplificado
        ]);

        return back()->with('success', 'Avaliação enviada com sucesso!');
    }
}
