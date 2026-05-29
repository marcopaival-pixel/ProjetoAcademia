<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenFoodFactsLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_food_search_returns_json_for_authenticated_user(): void
    {
        Http::fake([
            '*/cgi/search.pl*' => Http::response([
                'products' => [
                    ['code' => '3017620422003', 'product_name' => 'Exemplo', 'brands' => 'Marca'],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('food.search', ['q' => 'exemplo']))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('products.0.code', '3017620422003');
    }

    public function test_food_search_second_request_uses_cache_and_does_not_hit_api_again(): void
    {
        config(['services.openfoodfacts.cache_search_ttl_seconds' => 600]);
        Http::fake([
            '*/cgi/search.pl*' => Http::response([
                'products' => [
                    ['code' => '3017620422003', 'product_name' => 'Cacheado', 'brands' => 'Marca'],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('food.search', ['q' => 'aveia']))
            ->assertOk()
            ->assertJsonPath('products.0.name', 'Cacheado');

        $this->actingAs($user)
            ->getJson(route('food.search', ['q' => 'aveia']))
            ->assertOk()
            ->assertJsonPath('products.0.name', 'Cacheado');

        Http::assertSentCount(1);
    }

    public function test_food_search_cache_key_normalizes_whitespace_and_case(): void
    {
        config(['services.openfoodfacts.cache_search_ttl_seconds' => 600]);
        Http::fake([
            '*/cgi/search.pl*' => Http::response([
                'products' => [
                    ['code' => '3017620422003', 'product_name' => 'Um', 'brands' => ''],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('food.search', ['q' => '  EXEMPLO  ']))
            ->assertOk()
            ->assertJsonPath('products.0.name', 'Um');

        $this->actingAs($user)
            ->getJson(route('food.search', ['q' => 'exemplo']))
            ->assertOk()
            ->assertJsonPath('products.0.name', 'Um');

        Http::assertSentCount(1);
    }

    public function test_food_product_returns_nutrients_for_authenticated_user(): void
    {
        Http::fake([
            '*/api/v2/product/3017620422003.json*' => Http::response([
                'status' => 1,
                'product' => [
                    'product_name' => 'Exemplo',
                    'brands' => 'Marca',
                    'nutriments' => [
                        'energy-kcal_100g' => 100,
                        'proteins_100g' => 5,
                        'carbohydrates_100g' => 10,
                        'fat_100g' => 2,
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('food.product', ['code' => '3017620422003']))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('product.energy_kcal', 100)
            ->assertJsonPath('product.protein_g', 5);
    }

    public function test_food_product_second_request_uses_cache_and_does_not_hit_api_again(): void
    {
        config(['services.openfoodfacts.cache_product_ttl_seconds' => 3600]);
        Http::fake([
            '*/api/v2/product/3017620422003.json*' => Http::response([
                'status' => 1,
                'product' => [
                    'product_name' => 'Cacheado',
                    'brands' => 'Marca',
                    'nutriments' => [
                        'energy-kcal_100g' => 50,
                        'proteins_100g' => 1,
                        'carbohydrates_100g' => 2,
                        'fat_100g' => 3,
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('food.product', ['code' => '3017620422003']))
            ->assertOk()
            ->assertJsonPath('product.energy_kcal', 50);

        $this->actingAs($user)
            ->getJson(route('food.product', ['code' => '3017620422003']))
            ->assertOk()
            ->assertJsonPath('product.energy_kcal', 50);

        Http::assertSentCount(1);
    }

    public function test_food_search_requires_authentication(): void
    {
        $this->getJson(route('food.search', ['q' => 'test']))->assertUnauthorized();
    }

    public function test_openfoodfacts_routes_are_throttled_per_user(): void
    {
        config(['services.openfoodfacts.max_requests_per_minute' => 2]);
        Http::fake([
            '*/cgi/search.pl*' => Http::response(['products' => []], 200),
        ]);

        $user = User::factory()->create();
        $queries = ['aaa', 'bbb', 'ccc', 'ddd', 'eee', 'fff'];
        foreach (array_slice($queries, 0, 5) as $q) {
            $this->actingAs($user)->getJson(route('food.search', ['q' => $q]))->assertOk();
        }
        $this->actingAs($user)->getJson(route('food.search', ['q' => $queries[5]]))->assertStatus(429);
    }
}
