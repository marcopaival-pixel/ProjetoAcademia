<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Services\AiCreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiCreditBalanceController extends Controller
{
    use FormatsApiResponses;

    public function show(Request $request, AiCreditService $credits): JsonResponse
    {
        $wallet = $credits->getWallet($request->user());

        return $this->success([
            'balance' => (int) $wallet->balance,
            'monthly_allowance' => (int) $wallet->monthly_allowance,
            'extra_credits' => (int) $wallet->extra_credits,
            'renewal_date' => optional($wallet->renewal_date)->toDateString(),
            'expires_at' => optional($wallet->expires_at)->toDateString(),
        ]);
    }
}
