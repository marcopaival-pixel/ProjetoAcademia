<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AiCreditWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiCreditController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $wallet = AiCreditWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['balance' => 0, 'monthly_allowance' => 0, 'extra_credits' => 0]
        );

        return response()->json([
            'data' => [
                'balance' => (int) $wallet->balance,
                'monthly_allowance' => (int) $wallet->monthly_allowance,
                'extra_credits' => (int) $wallet->extra_credits,
                'renewal_date' => optional($wallet->renewal_date)->toDateString(),
                'expires_at' => optional($wallet->expires_at)->toDateString(),
            ],
        ]);
    }
}
