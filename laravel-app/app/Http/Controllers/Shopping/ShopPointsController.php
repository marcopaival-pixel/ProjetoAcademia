<?php

namespace App\Http\Controllers\Shopping;

use App\Http\Controllers\Controller;
use App\Services\Shop\ShopPointsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShopPointsController extends Controller
{
    public function __construct(private ShopPointsService $pointsService) {}

    public function index(): View
    {
        $user = Auth::user();
        $wallet = null;
        $transactions = collect();
        $error = null;

        try {
            $wallet = $this->pointsService->getOrCreateWallet($user);
            $transactions = $wallet->transactions()->orderByDesc('id')->limit(30)->get();
        } catch (\RuntimeException $e) {
            $error = $e->getMessage();
        }

        return view('shopping.points', compact('wallet', 'transactions', 'error'));
    }
}
