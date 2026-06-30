<?php

namespace App\Http\Controllers\Shopping;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShopDigitalDownloadController extends Controller
{
    public function __invoke(Request $request, string $token): StreamedResponse
    {
        $item = ShopOrderItem::query()
            ->where('download_token', $token)
            ->with(['order', 'product'])
            ->firstOrFail();

        if ($item->order?->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $this->orderAllowsDownload($item->order)) {
            abort(403, 'Download disponível apenas para pedidos pagos.');
        }

        if (! $item->canDownload()) {
            abort(403, 'Download expirado ou limite atingido.');
        }

        $path = $item->product?->downloadable_file;
        if ($path === null || $path === '' || ! Storage::disk('local')->exists($path)) {
            abort(404, 'Arquivo digital não encontrado.');
        }

        $item->increment('download_count');

        return Storage::disk('local')->download($path, basename($path));
    }

    private function orderAllowsDownload(ShopOrder $order): bool
    {
        return in_array($order->status, [
            ShopOrder::STATUS_PAID,
            ShopOrder::STATUS_PROCESSING,
            ShopOrder::STATUS_SHIPPED,
            ShopOrder::STATUS_DELIVERED,
            ShopOrder::STATUS_COMPLETED,
        ], true);
    }
}
