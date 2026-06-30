<?php

namespace App\Services\Shop;

use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShopOrderAccess
{
    public function assertOwnedBy(ShopOrder $order, ?User $user = null): void
    {
        $user ??= Auth::user();

        if ($user === null || $order->user_id !== $user->id) {
            abort(403);
        }
    }
}
