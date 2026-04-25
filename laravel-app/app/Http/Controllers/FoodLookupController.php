<?php

namespace App\Http\Controllers;

use App\Services\OpenFoodFactsClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodLookupController extends Controller
{
    /**
     * Pesquisa textual de produtos (Open Food Facts).
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $result = OpenFoodFactsClient::search(
            $validated['q'],
            1  // Sempre página 1 (sem paginação por enquanto)
        );

        if (! $result['ok']) {
            $msg = (string) ($result['error'] ?? 'Erro na consulta do alimento.');
            $status = 502; // Bad Gateway por padrão para erro na API externa
            
            $msgLower = strtolower($msg);
            if (str_contains($msgLower, '429')) {
                $status = 429;
            } elseif (str_contains($msgLower, '403')) {
                $status = 403;
            } elseif (str_contains($msgLower, 'não foi possível contactar') || str_contains($msgLower, 'timeout')) {
                $status = 504;
            }

            return response()->json([
                'ok' => false,
                'error' => $msg,
            ], $status);
        }

        return response()->json([
            'ok' => true,
            'products' => $result['products'],
            'source' => 'Open Food Facts',
        ]);
    }

    /**
     * Detalhe nutricional por código de barras (para preencher o diário).
     */
    public function product(Request $request, string $code): JsonResponse
    {
        $digits = preg_replace('/\D/', '', $code) ?? '';
        if (strlen($digits) < 8 || strlen($digits) > 14) {
            return response()->json([
                'ok' => false,
                'error' => 'Código de barras inválido.',
            ], 422);
        }

        $result = OpenFoodFactsClient::productByCode($digits);

        if (! $result['ok']) {
            $msg = (string) ($result['error'] ?? 'Produto não encontrado.');
            $status = 422;
            
            $msgLower = strtolower($msg);
            if (str_contains($msgLower, 'não encontrado')) {
                $status = 404;
            } elseif (str_contains($msgLower, '429')) {
                $status = 429;
            } elseif (str_contains($msgLower, '403')) {
                $status = 403;
            }

            return response()->json([
                'ok' => false,
                'error' => $msg,
            ], $status);
        }

        return response()->json([
            'ok' => true,
            'product' => $result['product'],
        ]);
    }
}
