<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReferralCodeResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralCodeController extends Controller
{
    /**
     * Verifica um código de indicação e calcula os valores de desconto.
     */
    public function verify(Request $request, ReferralCodeResolver $resolver): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'plan_id' => 'nullable|integer|exists:plans,id',
        ]);

        $resolved = $resolver->resolve(
            $request->input('code'),
            $request->filled('plan_id') ? (int) $request->input('plan_id') : null
        );

        if ($resolved === null) {
            return response()->json([
                'valid' => false,
                'message' => 'Código de indicação inválido ou incompatível com o plano selecionado.',
            ], 422);
        }

        $response = [
            'valid' => true,
            'source' => $resolved['source'],
            'representative_name' => $resolved['representative_name'],
            'discount_rate' => $resolved['discount_rate'],
            'discount_amount' => $resolved['discount_amount'],
            'message' => 'Código aplicado com sucesso!',
        ];

        if ($request->filled('plan_id')) {
            $plan = \App\Models\Plan::find($request->input('plan_id'));
            if ($plan) {
                $response['original_price'] = (float) $plan->price;
                $response['final_price'] = max(0, round((float) $plan->price - $resolved['discount_amount'], 2));
            }
        }

        return response()->json($response);
    }
}
