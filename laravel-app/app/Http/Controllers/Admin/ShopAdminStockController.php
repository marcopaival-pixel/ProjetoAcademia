<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Shop\ShopStockAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopAdminStockController extends Controller
{
    public function __construct(private ShopStockAlertService $stockAlertService) {}

    public function index(): View
    {
        $lowStock = $this->stockAlertService->lowStockProducts();
        $outOfStock = $this->stockAlertService->outOfStockProducts();

        return view('admin.shop.stock.index', compact('lowStock', 'outOfStock'));
    }

    public function notify(Request $request): RedirectResponse
    {
        $count = $this->stockAlertService->notifyAdminsLowStock();

        return redirect()
            ->route('admin.shop.stock.index')
            ->with(
                $count > 0 ? 'success' : 'error',
                $count > 0
                    ? "Alerta enviado a {$count} administrador(es)."
                    : 'Nenhum produto com estoque baixo no momento.'
            );
    }
}
