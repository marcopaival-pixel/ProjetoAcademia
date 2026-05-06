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

        $query = strtolower(trim($validated['q']));
        $cacheKey = "food_search_" . md5($query);

        $result = \Cache::remember($cacheKey, 86400, function() use ($query) {
            return OpenFoodFactsClient::search($query, 1);
        });

        if (! $result['ok']) {
            // Se falhou, não cacheamos o erro por 24h, apenas por 1 minuto para evitar spam
            \Cache::forget($cacheKey);
            
            $msg = (string) ($result['error'] ?? 'Erro na consulta do alimento.');
            $status = 502;
            
            $msgLower = strtolower($msg);
            if (str_contains($msgLower, '429')) {
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
            'products' => $result['products'],
            'source' => 'Open Food Facts (Cached)',
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

        $cacheKey = "food_product_{$digits}";

        $result = \Cache::remember($cacheKey, 86400, function() use ($digits) {
            return OpenFoodFactsClient::productByCode($digits);
        });

        if (! $result['ok']) {
            \Cache::forget($cacheKey);
            
            $msg = (string) ($result['error'] ?? 'Produto não encontrado.');
            $status = 422;
            
            $msgLower = strtolower($msg);
            if (str_contains($msgLower, 'não encontrado')) {
                $status = 404;
            }

            return response()->json([
                'ok' => false,
                'error' => $msg,
            ], $status);
        }

        return response()->json([
            'ok' => true,
            'product' => $result['product'],
            'cached' => true,
        ]);
    }
}
