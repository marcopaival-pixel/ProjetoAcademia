<?php

namespace App\Services;

use App\Models\User;
use App\Models\AiCreditUsageLog;
use App\Models\AiCreditPurchaseLog;
use App\Models\AiCreditPackage;
use App\Services\FinancialLogService;
use App\Notifications\LowAiCreditsNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AiCreditService
{
    /**
     * Costs for different AI actions in credits.
     */
    public const COSTS = [
        'analyze_meal_text' => 1,
        'generate_diet' => 5,
        'generate_workout' => 3,
        'analyze_meal_photo' => 4,
        'analyze_body_photo' => 4,
        'generate_report' => 2,
        'chat_response' => 1,
        'distribution' => 0, // Distribution itself doesn't cost extra besides the amount moved
    ];

    /**
     * Get the credit balance for a user.
     */
    public function getBalance(User $user): int
    {
        return $user->ai_credits;
    }

    /**
     * Check if a user has enough credits for an action.
     */
    public function hasCredits(User $user, string $actionType): bool
    {
        $cost = self::COSTS[$actionType] ?? 1;
        return $user->ai_credits >= $cost;
    }

    /**
     * Consume credits for an AI action.
     */
    public function consume(User $user, string $actionType, array $metadata = [], ?string $cacheKey = null): bool
    {
        $cost = self::COSTS[$actionType] ?? 1;

        if ($user->ai_credits < $cost) {
            return false;
        }

        return DB::transaction(function () use ($user, $actionType, $cost, $metadata, $cacheKey) {
            // Update user balance
            $user->decrement('ai_credits', $cost);

            // Log usage
            AiCreditUsageLog::create([
                'user_id' => $user->id,
                'action_type' => $actionType,
                'credits_consumed' => $cost,
                'metadata' => $metadata,
                'response_cache_key' => $cacheKey,
            ]);

            // Check for low credits (20% rule)
            $this->checkBalanceAndNotify($user);

            return true;
        });
    }

    /**
     * Check user balance and send notification if low or exhausted.
     */
    public function checkBalanceAndNotify(User $user): void
    {
        $balance = $user->ai_credits;

        if ($balance <= 0) {
            $user->notify(new LowAiCreditsNotification(0, true));
            return;
        }

        // Get last purchase to calculate 20%
        $lastPurchase = AiCreditPurchaseLog::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->latest()
            ->first();

        $threshold = 5; // Default threshold if no purchase found
        if ($lastPurchase) {
            $threshold = max(5, floor($lastPurchase->credits_amount * 0.2));
        }

        if ($balance < $threshold) {
            // Avoid spamming - notify only if not notified recently for this threshold
            $cacheKey = "notified_low_credits_{$user->id}_{$balance}";
            if (!Cache::has($cacheKey)) {
                $user->notify(new LowAiCreditsNotification($balance));
                Cache::put($cacheKey, true, now()->addDays(1));
            }
        }
    }

    /**
     * Add credits to a user (e.g., after purchase).
     */
    public function addCredits(User $user, int $amount, string $packageName, float $price, string $paymentMethod, string $paymentId): AiCreditPurchaseLog
    {
        return DB::transaction(function () use ($user, $amount, $packageName, $price, $paymentMethod, $paymentId) {
            // Update user balance
            $user->increment('ai_credits', $amount);

            // Log purchase
            $log = AiCreditPurchaseLog::create([
                'user_id' => $user->id,
                'package_name' => $packageName,
                'credits_amount' => $amount,
                'price' => $price,
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
                'payment_id' => $paymentId,
            ]);

            FinancialLogService::log([
                'user_id' => $user->id,
                'action' => 'AI_CREDITS_PURCHASE',
                'amount' => $price,
                'origin' => $paymentMethod,
                'payload' => ['package' => $packageName, 'credits' => $amount, 'payment_id' => $paymentId]
            ]);

            return $log;
        });
    }

    /**
     * Distribute credits from a clinic to a user (professional or patient).
     */
    public function distribute(User $clinic, User $target, int $amount): bool
    {
        if ($clinic->ai_credits < $amount) {
            return false;
        }

        return DB::transaction(function () use ($clinic, $target, $amount) {
            $clinic->decrement('ai_credits', $amount);
            $target->increment('ai_credits', $amount);

            // Log as usage for clinic with distribution metadata
            AiCreditUsageLog::create([
                'user_id' => $clinic->id,
                'action_type' => 'distribution',
                'credits_consumed' => $amount,
                'metadata' => [
                    'target_user_id' => $target->id,
                    'target_user_name' => $target->name,
                ],
            ]);

            return true;
        });
    }

    /**
     * Try to get a cached response to save credits.
     */
    public function getCachedResponse(string $cacheKey): ?string
    {
        return Cache::get("ai_response_{$cacheKey}");
    }

    /**
     * Cache an AI response.
     */
    public function cacheResponse(string $cacheKey, string $response, int $ttlSeconds = 86400): void
    {
        Cache::put("ai_response_{$cacheKey}", $response, $ttlSeconds);
    }

    /**
     * Get variables for AI pricing strategy.
     */
    public function getPricingVariables(): array
    {
        return [
            'monthly_cost' => (float) \App\Models\AdminSetting::get('ai_monthly_total_cost', 500.00),
            'monthly_usage' => (int) \App\Models\AdminSetting::get('ai_monthly_total_usage', 5000),
            'profit_margin' => (float) \App\Models\AdminSetting::get('ai_profit_margin', 300.00), // 300%
        ];
    }

    /**
     * Calculate the cost per single AI use (credit).
     */
    public function calculateCostPerUse(): float
    {
        $vars = $this->getPricingVariables();
        if ($vars['monthly_usage'] <= 0) return 0.10; // Fallback

        return $vars['monthly_cost'] / $vars['monthly_usage'];
    }

    /**
     * Calculate the final price for a single credit based on margin.
     */
    public function calculateCreditPrice(): float
    {
        $costPerUse = $this->calculateCostPerUse();
        $margin = $this->getPricingVariables()['profit_margin'] / 100;

        return $costPerUse * $margin;
    }

    /**
     * Calculate the price for a package of credits.
     */
    public function calculatePackagePrice(int $credits): array
    {
        $pricePerCredit = $this->calculateCreditPrice();
        $totalCost = $this->calculateCostPerUse() * $credits;
        $finalPrice = $pricePerCredit * $credits;
        $profit = $finalPrice - $totalCost;

        return [
            'credits' => $credits,
            'cost' => round($totalCost, 2),
            'price' => round($finalPrice, 2),
            'profit' => round($profit, 2),
            'margin' => $this->getPricingVariables()['profit_margin']
        ];
    }
}
