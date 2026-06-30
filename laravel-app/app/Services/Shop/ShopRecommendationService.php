<?php

namespace App\Services\Shop;

use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProduct;
use App\Models\ShopRecommendation;
use App\Models\User;
use Illuminate\Support\Collection;

class ShopRecommendationService
{
    private const CACHE_HOURS = 24;

    /** @var array<string, list<string>> */
    private const PROFILE_GOAL_TO_PRODUCT_TAGS = [
        'lose_aggressive' => ['emagrecimento'],
        'lose' => ['emagrecimento'],
        'gain' => ['hipertrofia'],
        'recomp' => ['emagrecimento', 'hipertrofia'],
        'maintain' => ['saude'],
        'performance' => ['performance'],
    ];

    /**
     * @return Collection<int, ShopProduct>
     */
    public function recommendedProductsFor(User $user, int $limit = 8): Collection
    {
        $companyId = $user->academy_company_id;
        if ($companyId === null) {
            return collect();
        }

        $cached = $this->findValidCache($user->id, $companyId);

        if ($cached !== null && is_array($cached->product_ids) && $cached->product_ids !== []) {
            return $this->loadProducts($cached->product_ids);
        }

        return $this->persistAndLoad($user, $companyId, $limit);
    }

    /**
     * Força recálculo (fila / pós-compra).
     *
     * @return Collection<int, ShopProduct>
     */
    public function refreshForUser(User $user, int $limit = 8): Collection
    {
        $companyId = $user->academy_company_id;
        if ($companyId === null) {
            return collect();
        }

        return $this->persistAndLoad($user, $companyId, $limit);
    }

    private function findValidCache(int $userId, int $companyId): ?ShopRecommendation
    {
        return ShopRecommendation::query()
            ->where('user_id', $userId)
            ->where('academy_company_id', $companyId)
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    /**
     * @return Collection<int, ShopProduct>
     */
    private function persistAndLoad(User $user, int $companyId, int $limit): Collection
    {
        [$productIds, $reason, $context] = $this->buildRecommendationSet($user, $companyId, $limit);

        ShopRecommendation::query()
            ->where('user_id', $user->id)
            ->where('academy_company_id', $companyId)
            ->delete();

        if ($productIds !== []) {
            ShopRecommendation::create([
                'user_id' => $user->id,
                'academy_company_id' => $companyId,
                'product_ids' => $productIds,
                'reason' => $reason,
                'context' => $context,
                'expires_at' => now()->addHours(self::CACHE_HOURS),
            ]);
        }

        return $this->loadProducts($productIds);
    }

    /**
     * @return array{0: list<int>, 1: string, 2: array<string, mixed>}
     */
    private function buildRecommendationSet(User $user, int $companyId, int $limit): array
    {
        $paidStatuses = [
            ShopOrder::STATUS_PAID,
            ShopOrder::STATUS_PROCESSING,
            ShopOrder::STATUS_SHIPPED,
            ShopOrder::STATUS_DELIVERED,
            ShopOrder::STATUS_COMPLETED,
        ];

        $purchasedProductIds = ShopOrderItem::query()
            ->whereHas('order', function ($query) use ($user, $paidStatuses) {
                $query->where('user_id', $user->id)
                    ->whereIn('status', $paidStatuses);
            })
            ->pluck('product_id')
            ->unique()
            ->filter()
            ->values();

        if ($purchasedProductIds->isNotEmpty()) {
            $categoryIds = ShopProduct::query()
                ->whereIn('id', $purchasedProductIds)
                ->pluck('category_id')
                ->unique()
                ->filter()
                ->values();

            $productIds = ShopProduct::published()
                ->where('academy_company_id', $companyId)
                ->when($categoryIds->isNotEmpty(), fn ($q) => $q->whereIn('category_id', $categoryIds))
                ->whereNotIn('id', $purchasedProductIds)
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->limit($limit)
                ->pluck('id')
                ->all();

            return [
                $productIds,
                'purchase_history',
                [
                    'purchased_product_ids' => $purchasedProductIds->all(),
                    'category_ids' => $categoryIds->all(),
                ],
            ];
        }

        $goalTags = $this->userGoalTags($user);
        if ($goalTags !== []) {
            $productIds = $this->productsMatchingGoalTags($companyId, $goalTags, $limit);

            if ($productIds !== []) {
                return [
                    $productIds,
                    'goal_match',
                    [
                        'goal_tags' => $goalTags,
                        'profile_goal' => $user->profile?->goal,
                    ],
                ];
            }

            $productIds = $this->productsMatchingAiTags($companyId, $goalTags, $limit);

            if ($productIds !== []) {
                return [
                    $productIds,
                    'ai_tag_match',
                    [
                        'interest_tags' => $goalTags,
                        'profile_goal' => $user->profile?->goal,
                    ],
                ];
            }
        }

        $productIds = ShopProduct::published()
            ->where('academy_company_id', $companyId)
            ->featured()
            ->orderByDesc('published_at')
            ->limit($limit)
            ->pluck('id')
            ->all();

        if ($productIds === []) {
            $productIds = ShopProduct::published()
                ->where('academy_company_id', $companyId)
                ->orderByDesc('published_at')
                ->limit($limit)
                ->pluck('id')
                ->all();
        }

        return [$productIds, 'featured_fallback', ['source' => 'catalog']];
    }

    /**
     * @return list<string>
     */
    private function userGoalTags(User $user): array
    {
        $profileGoal = $user->profile?->goal;

        if ($profileGoal === null || $profileGoal === '') {
            return [];
        }

        return self::PROFILE_GOAL_TO_PRODUCT_TAGS[$profileGoal] ?? [];
    }

    /**
     * @param  list<string>  $goalTags
     * @return list<int>
     */
    private function productsMatchingGoalTags(int $companyId, array $goalTags, int $limit): array
    {
        return ShopProduct::published()
            ->where('academy_company_id', $companyId)
            ->where(function ($query) use ($goalTags) {
                foreach ($goalTags as $tag) {
                    $query->orWhereJsonContains('goal_types', $tag);
                }
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->pluck('id')
            ->all();
    }

    /**
     * @param  list<string>  $tags
     * @return list<int>
     */
    private function productsMatchingAiTags(int $companyId, array $tags, int $limit): array
    {
        return ShopProduct::published()
            ->where('academy_company_id', $companyId)
            ->where(function ($query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->orWhereJsonContains('ai_tags', $tag);
                }
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->pluck('id')
            ->all();
    }

    /**
     * @param  list<int>  $ids
     * @return Collection<int, ShopProduct>
     */
    private function loadProducts(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        $products = ShopProduct::published()
            ->with('images', 'category')
            ->whereIn('id', $ids)
            ->get();

        return $products
            ->sortBy(fn (ShopProduct $product) => array_search($product->id, $ids, true))
            ->values();
    }
}
