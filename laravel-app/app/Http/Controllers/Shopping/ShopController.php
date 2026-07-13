<?php

namespace App\Http\Controllers\Shopping;

use App\Http\Controllers\Controller;
use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\ShopWishlist;
use App\Services\Shop\ShopCartService;
use App\Services\Shop\ShopRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function __construct(
        private ShopCartService $cartService,
        private ShopRecommendationService $recommendationService,
    ) {}

    /**
     * Vitrine principal — produtos em destaque + categorias.
     */
    public function index()
    {
        $user = Auth::user();

        $featured = ShopProduct::published()
            ->featured()
            ->with('images', 'category')
            ->orderByDesc('published_at')
            ->take(8)
            ->get();

        $recent = ShopProduct::published()
            ->with('images', 'category')
            ->orderByDesc('published_at')
            ->take(12)
            ->get();

        $categories = ShopCategory::active()
            ->root()
            ->orderBy('sort_order')
            ->get();

        $cartSummary = ($user && $user->academy_company_id) ? $this->cartService->summary($user) : [
            'cart' => null,
            'subtotal' => 0.0,
            'discount' => 0.0,
            'shipping' => 0.0,
            'total' => 0.0,
            'coupon' => null,
        ];

        $wishlistIds = ($user && $user->academy_company_id) ? ShopWishlist::where('user_id', $user->id)
            ->pluck('product_id')
            ->toArray() : [];

        $recommended = ($user && $user->academy_company_id) ? $this->recommendationService->recommendedProductsFor($user) : collect();

        return view('shopping.index', compact(
            'featured',
            'recent',
            'categories',
            'cartSummary',
            'wishlistIds',
            'recommended',
        ));
    }

    /**
     * Resultados de busca.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type  = $request->get('tipo');
        $catId = $request->get('categoria');
        $minPrice = $request->get('preco_min');
        $maxPrice = $request->get('preco_max');
        $sort = $request->get('ordem', 'recent');

        $products = ShopProduct::published()
            ->with('images', 'category')
            ->when($query, fn ($q) => $q->where(function ($inner) use ($query) {
                $inner->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%");
            }))
            ->when($type, fn ($q) => $q->ofType($type))
            ->when($catId, fn ($q) => $q->where('category_id', $catId))
            ->when($minPrice, fn ($q) => $q->where('price', '>=', $minPrice))
            ->when($maxPrice, fn ($q) => $q->where('price', '<=', $maxPrice))
            ->when($sort, function ($q) use ($sort) {
                if ($sort === 'price_asc') {
                    $q->orderBy('price', 'asc');
                } elseif ($sort === 'price_desc') {
                    $q->orderBy('price', 'desc');
                } else {
                    $q->orderByDesc('is_featured')->orderByDesc('published_at');
                }
            })
            ->paginate(16)
            ->withQueryString();

        $categories = ShopCategory::active()->orderBy('sort_order')->get();

        return view('shopping.search', compact('products', 'query', 'categories', 'type', 'catId', 'minPrice', 'maxPrice', 'sort'));
    }

    /**
     * Produtos de uma categoria.
     */
    public function category(string $slug)
    {
        $category = ShopCategory::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $products = ShopProduct::published()
            ->where('category_id', $category->id)
            ->with('images')
            ->orderByDesc('is_featured')
            ->paginate(16);

        return view('shopping.category', compact('category', 'products'));
    }
}
