<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Nutrient;
use App\Models\ApiIntegrationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NutritionService
{
    /**
     * Busca um alimento localmente ou via API externa.
     */
    public function findOrFetch(string $query): ?Food
    {
        $query = trim($query);
        $isBarcode = preg_match('/^\d{8,14}$/', $query);

        // 1. Busca Local
        $food = $isBarcode 
            ? Food::where('barcode', $query)->first()
            : Food::where('name', 'like', "%{$query}%")->first();

        if ($food) {
            return $food->load('nutrients');
        }

        // 2. Busca API Externa
        return $this->fetchFromExternalApi($query, $isBarcode);
    }

    /**
     * Integração direta com Open Food Facts e persistência local.
     */
    protected function fetchFromExternalApi(string $query, bool $isBarcode): ?Food
    {
        $startTime = microtime(true);
        $apiResult = null;
        $endpoint = $isBarcode ? "product/{$query}" : "search?q={$query}";

        try {
            if ($isBarcode) {
                $apiResult = OpenFoodFactsClient::productByCode($query);
            } else {
                $search = OpenFoodFactsClient::search($query);
                if ($search['ok'] && !empty($search['products'])) {
                    // Pega o primeiro resultado e busca detalhes
                    $apiResult = OpenFoodFactsClient::productByCode($search['products'][0]['code']);
                }
            }

            $duration = (int)((microtime(true) - $startTime) * 1000);

            // Log da Integração
            ApiIntegrationLog::create([
                'api_name' => 'Open Food Facts',
                'endpoint' => $endpoint,
                'status_code' => ($apiResult && $apiResult['ok']) ? 200 : 404,
                'response_time_ms' => $duration,
                'response_payload' => $apiResult,
            ]);

            if ($apiResult && $apiResult['ok']) {
                return $this->persistFood($apiResult['product']);
            }

        } catch (\Exception $e) {
            ApiIntegrationLog::create([
                'api_name' => 'Open Food Facts',
                'endpoint' => $endpoint,
                'status_code' => 500,
                'error_message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Salva o alimento e seus nutrientes no banco local.
     */
    protected function persistFood(array $data): Food
    {
        return DB::transaction(function () use ($data) {
            $food = Food::updateOrCreate(
                ['barcode' => $data['code']],
                [
                    'name' => $data['name'],
                    'brand' => $data['brands'] ?? null,
                    'base_amount' => 100, // OFF sempre retorna /100g
                    'unit' => 'g',
                    'data_source' => 'openfoodfacts'
                ]
            );

            $nutrientMappings = Nutrient::all();
            $syncData = [];

            foreach ($nutrientMappings as $nutrient) {
                if (isset($data[$nutrient->slug])) {
                    $syncData[$nutrient->id] = ['amount' => $data[$nutrient->slug]];
                }
            }

            $food->nutrients()->sync($syncData);

            return $food->load('nutrients');
        });
    }
}
