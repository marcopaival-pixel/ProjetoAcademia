<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Cliente de leitura para {@link https://world.openfoodfacts.org Open Food Facts} (API gratuita, sem chave).
 * User-Agent descritivo exigido pela comunidade do projeto.
 */
final class OpenFoodFactsClient
{
    private const DEFAULT_BASE = 'https://pt.openfoodfacts.org';

    /**
     * @return array{ok: true, products: list<array{code: string, name: string, brands: string}>}|array{ok: false, error: string}
     */
    public static function search(string $query, int $page = 1): array
    {
        $query = trim($query);
        if (mb_strlen($query) < 2) {
            return ['ok' => false, 'error' => 'Indique pelo menos 2 caracteres para pesquisar.'];
        }

        $page = max(1, min($page, 20));

        $ttl = (int) config('services.openfoodfacts.cache_search_ttl_seconds', 600);
        if ($ttl > 0) {
            $cached = Cache::get(self::searchCacheKey($query, $page));
            if (is_array($cached) && ($cached['ok'] ?? false) === true) {
                return $cached;
            }
        }

        $result = self::fetchSearchFromApi($query, $page);

        if ($ttl > 0 && ($result['ok'] ?? false) === true) {
            Cache::put(self::searchCacheKey($query, $page), $result, $ttl);
        }

        return $result;
    }

    /**
     * @return array{ok: true, products: list<array{code: string, name: string, brands: string}>}|array{ok: false, error: string}
     */
    private static function fetchSearchFromApi(string $query, int $page): array
    {
        $base = rtrim((string) config('services.openfoodfacts.base_url', self::DEFAULT_BASE), '/');

        try {
            $response = Http::timeout(20)
                ->withoutVerifying()
                ->withHeaders(self::headers())
                ->get($base.'/cgi/search.pl', [
                    'search_terms' => $query,
                    'search_simple' => 1,
                    'action' => 'process',
                    'json' => 1,
                    'page_size' => 15,
                    'page' => $page,
                ]);
        } catch (\Throwable) {
            return ['ok' => false, 'error' => 'Não foi possível contactar o Open Food Facts. Tente mais tarde.'];
        }

        if (! $response->successful()) {
            return ['ok' => false, 'error' => 'Resposta inválida do Open Food Facts ('.$response->status().').'];
        }

        $data = $response->json();
        if (! is_array($data)) {
            return ['ok' => false, 'error' => 'Formato de resposta inesperado.'];
        }

        $raw = $data['products'] ?? [];
        if (! is_array($raw)) {
            return ['ok' => true, 'products' => []];
        }

        $products = [];
        foreach ($raw as $p) {
            if (! is_array($p)) {
                continue;
            }
            $code = (string) ($p['code'] ?? '');
            if ($code === '' || ! preg_match('/^\d{8,14}$/', $code)) {
                continue;
            }
            $name = trim((string) ($p['product_name'] ?? $p['generic_name'] ?? ''));
            if ($name === '') {
                $name = 'Sem nome';
            }
            if (mb_strlen($name) > 120) {
                $name = mb_substr($name, 0, 117).'…';
            }
            $brands = trim((string) ($p['brands'] ?? ''));
            if (mb_strlen($brands) > 60) {
                $brands = mb_substr($brands, 0, 57).'…';
            }
            $products[] = [
                'code' => $code,
                'name' => $name,
                'brands' => $brands,
            ];
        }

        return ['ok' => true, 'products' => $products];
    }

    private static function searchCacheKey(string $query, int $page): string
    {
        $baseFingerprint = sha1((string) config('services.openfoodfacts.base_url', self::DEFAULT_BASE));
        $norm = self::normalizeSearchTermsForCache($query);
        $queryFingerprint = sha1($norm.'|'.$page);

        return 'openfoodfacts:v1:search:'.$baseFingerprint.':'.$queryFingerprint;
    }

    private static function normalizeSearchTermsForCache(string $query): string
    {
        $q = trim($query);
        $collapsed = preg_replace('/\s+/u', ' ', $q);
        if (! is_string($collapsed)) {
            $collapsed = $q;
        }

        return mb_strtolower($collapsed);
    }

    /**
     * @return array{
     *   ok: true,
     *   product: array{
     *     code: string,
     *     name: string,
     *     brands: string,
     *     calories: int,
     *     protein_g: float,
     *     carbs_g: float,
     *     fat_g: float,
     *     basis: string,
     *     attribution: string
     *   }
     * }|array{ok: false, error: string}
     */
    public static function productByCode(string $code): array
    {
        if (! preg_match('/^\d{8,14}$/', $code)) {
            return ['ok' => false, 'error' => 'Código de barras inválido.'];
        }

        $ttl = (int) config('services.openfoodfacts.cache_product_ttl_seconds', 3600);
        if ($ttl > 0) {
            $cached = Cache::get(self::productCacheKey($code));
            if (is_array($cached) && ($cached['ok'] ?? false) === true) {
                return $cached;
            }
        }

        $result = self::fetchProductByCodeFromApi($code);

        if ($ttl > 0 && ($result['ok'] ?? false) === true) {
            Cache::put(self::productCacheKey($code), $result, $ttl);
        }

        return $result;
    }

    private static function productCacheKey(string $code): string
    {
        $baseFingerprint = sha1((string) config('services.openfoodfacts.base_url', self::DEFAULT_BASE));

        return 'openfoodfacts:v1:product:'.$baseFingerprint.':'.$code;
    }

    /**
     * @return array{ok: true, product: array<string, mixed>}|array{ok: false, error: string}
     */
    private static function fetchProductByCodeFromApi(string $code): array
    {
        $base = rtrim((string) config('services.openfoodfacts.base_url', self::DEFAULT_BASE), '/');
        $url = $base.'/api/v2/product/'.rawurlencode($code).'.json';
        $fields = 'product_name,brands,generic_name,nutriments,nutrition_data_per,serving_size,serving_quantity';

        try {
            $response = Http::timeout(20)
                ->withoutVerifying()
                ->withHeaders(self::headers())
                ->get($url, ['fields' => $fields]);
        } catch (\Throwable) {
            return ['ok' => false, 'error' => 'Não foi possível contactar o Open Food Facts. Tente mais tarde.'];
        }

        if (! $response->successful()) {
            return ['ok' => false, 'error' => 'Resposta inválida do Open Food Facts ('.$response->status().').'];
        }

        $payload = $response->json();
        if (! is_array($payload) || ($payload['status'] ?? 0) !== 1) {
            return ['ok' => false, 'error' => 'Produto não encontrado nesta base.'];
        }

        $p = $payload['product'] ?? null;
        if (! is_array($p)) {
            return ['ok' => false, 'error' => 'Dados do produto incompletos.'];
        }

        $nut = $p['nutriments'] ?? [];
        $nut = is_array($nut) ? $nut : [];

        $parsed = self::parseNutrimentsPer100g($nut);
        if ($parsed === null) {
            return ['ok' => false, 'error' => 'Este produto não tem valores nutricionais por 100 g (ou equivalente) na base.'];
        }

        $name = trim((string) ($p['product_name'] ?? $p['generic_name'] ?? ''));
        if ($name === '') {
            $name = 'Produto '.$code;
        }
        $brands = trim((string) ($p['brands'] ?? ''));

        return [
            'ok' => true,
            'product' => array_merge([
                'code' => $code,
                'name' => $name,
                'brands' => $brands,
                'attribution' => 'Open Food Facts (openfoodfacts.org) — dados colaborativos; confirme no rótulo.',
            ], $parsed),
        ];
    }

    /**
     * @param  array<string, mixed>  $nut
     * @return array<string, mixed>|null
     */
    private static function parseNutrimentsPer100g(array $nut): ?array
    {
        $kcal = self::num($nut['energy-kcal_100g'] ?? null);
        if ($kcal === null) {
            $kj = self::num($nut['energy-kj_100g'] ?? null);
            if ($kj !== null) {
                $kcal = $kj / 4.184;
            }
        }
        if ($kcal === null) {
            $kcal = self::num($nut['energy-kcal'] ?? null);
        }
        if ($kcal === null) {
            $kj = self::num($nut['energy-kj'] ?? null);
            if ($kj !== null) {
                $kcal = $kj / 4.184;
            }
        }

        if ($kcal === null) {
            return null;
        }

        return [
            'energy_kcal'    => (int) max(0, min(20000, (int) round($kcal))),
            'protein_g'      => round(max(0, min(600, self::num($nut['proteins_100g'] ?? $nut['proteins'] ?? 0))), 1),
            'carbohydrates_g'=> round(max(0, min(600, self::num($nut['carbohydrates_100g'] ?? $nut['carbohydrates'] ?? 0))), 1),
            'fat_g'          => round(max(0, min(600, self::num($nut['fat_100g'] ?? $nut['fat'] ?? 0))), 1),
            'fat_saturated_g'=> round(max(0, min(100, self::num($nut['saturated-fat_100g'] ?? $nut['saturated-fat'] ?? 0))), 1),
            'fat_trans_g'    => round(max(0, min(100, self::num($nut['trans-fat_100g'] ?? $nut['trans-fat'] ?? 0))), 1),
            'fiber_g'        => round(max(0, min(100, self::num($nut['fiber_100g'] ?? $nut['fiber'] ?? 0))), 1),
            'sugars_g'       => round(max(0, min(100, self::num($nut['sugars_100g'] ?? $nut['sugars'] ?? 0))), 1),
            'sodium_mg'      => round(max(0, min(100000, self::num($nut['sodium_100g'] ?? $nut['sodium'] ?? 0) * 1000)), 1),
            'calcium_mg'     => round(max(0, min(100000, self::num($nut['calcium_100g'] ?? $nut['calcium'] ?? 0) * 1000)), 1),
            'iron_mg'        => round(max(0, min(10000, self::num($nut['iron_100g'] ?? $nut['iron'] ?? 0) * 1000)), 1),
            'potassium_mg'   => round(max(0, min(100000, self::num($nut['potassium_100g'] ?? $nut['potassium'] ?? 0) * 1000)), 1),
            'vitamin_c_mg'   => round(max(0, min(10000, self::num($nut['vitamin-c_100g'] ?? $nut['vitamin-c'] ?? 0) * 1000)), 1),
            'vitamin_a_mcg'  => round(max(0, min(100000, self::num($nut['vitamin-a_100g'] ?? $nut['vitamin-a'] ?? 0) * 1000000)), 1), // Vitamin A is in g or mcg? Usually mcg. OFF has many units.
            'basis' => 'Valores por 100 g.',
        ];
    }

    private static function num(mixed $v): ?float
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            $f = (float) $v;

            return is_finite($f) ? $f : null;
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private static function headers(): array
    {
        $app = config('app.name', 'Laravel');
        $url = rtrim((string) config('app.url', 'https://localhost'), '/');

        return [
            'Accept' => 'application/json',
            'Accept-Language' => 'pt-BR,pt;q=0.9,en;q=0.8',
            'User-Agent' => 'ProjetoAcademia/1.0 (estudo_estudo@example.com)',
        ];
    }
}
