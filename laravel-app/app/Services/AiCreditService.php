<?php

namespace App\Services;

use App\Models\User;
use App\Models\AiCreditWallet;
use App\Models\AiCreditTransaction;
use App\Models\AiFeatureCost;
use App\Models\AiCreditPackage;
use App\Services\FinancialLogService;
use App\Notifications\LowAiCreditsNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AiCreditService
{
    /**
     * Get or create the credit wallet for a user.
     */
    public function getWallet(User $user): AiCreditWallet
    {
        $legacyCredits = (int) ($user->ai_credits ?? 0);

        /** @var AiCreditWallet $wallet */
        $wallet = AiCreditWallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => $legacyCredits,
                'monthly_allowance' => 0,
                'extra_credits' => $legacyCredits,
                'renewal_date' => now()->addMonth(),
            ]
        );

        return $wallet;
    }

    /**
     * Get the credit balance for a user.
     */
    public function getBalance(User $user): int
    {
        return $this->getWallet($user)->balance;
    }

    /**
     * Check if a user has enough credits for an action.
     */
    public function hasCredits(User $user, string $featureCode): bool
    {
        $cost = AiFeatureCost::where('feature_code', $featureCode)->where('is_active', true)->first();
        if (!$cost) {
            return true; // Se não configurado, assume grátis ou erro de config
        }

        return $this->getBalance($user) >= $cost->credits_required;
    }

    /**
     * Consume credits for an AI action.
     */
    public function consume(User $user, string $featureCode, array $metadata = [], ?string $referenceId = null): bool
    {
        // Administradores não consomem créditos
        if ($user->isAdministrator()) {
            return true;
        }

        $featureCost = AiFeatureCost::where('feature_code', $featureCode)->where('is_active', true)->first();
        if (!$featureCost) {
            return true;
        }

        $cost = $featureCost->credits_required;
        $wallet = $this->getWallet($user);

        if ($wallet->balance < $cost) {
            return false;
        }

        return DB::transaction(function () use ($user, $wallet, $featureCode, $featureCost, $cost, $metadata, $referenceId) {
            $balanceBefore = $wallet->balance;
            
            // Lógica de consumo: primeiro consome do allowance (mensal), depois dos extras
            if ($wallet->monthly_allowance >= $cost) {
                $wallet->decrement('monthly_allowance', $cost);
            } else {
                $remaining = $cost - $wallet->monthly_allowance;
                $wallet->monthly_allowance = 0;
                $wallet->decrement('extra_credits', $remaining);
            }

            $wallet->decrement('balance', $cost);
            $wallet->save();

            // Registrar Transação
            AiCreditTransaction::create([
                'user_id' => $user->id,
                'type' => 'usage',
                'credits' => -$cost,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'feature_code' => $featureCode,
                'reference_id' => $referenceId,
                'description' => "Consumo de IA: {$featureCost->feature_name}",
            ]);

            // Notificar se saldo estiver baixo
            $this->checkBalanceAndNotify($user);

            return true;
        });
    }

    /**
     * Renova os créditos mensais do usuário com base no seu plano.
     */
    public function renewMonthly(User $user): void
    {
        $plan = $user->plan;
        if (!$plan) return;

        $allowance = $plan->ai_credits ?? 0;
        $wallet = $this->getWallet($user);

        DB::transaction(function () use ($user, $wallet, $allowance) {
            $balanceBefore = $wallet->balance;
            
            // Créditos mensais não costumam ser cumulativos em SaaS.
            // O novo balance será: extra_credits (que sobraram) + novo allowance do plano.
            $wallet->monthly_allowance = $allowance;
            $wallet->balance = $wallet->extra_credits + $allowance;
            $wallet->renewal_date = now()->addMonth();
            $wallet->save();

            AiCreditTransaction::create([
                'user_id' => $user->id,
                'type' => 'monthly',
                'credits' => $allowance,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => "Renovação mensal de créditos - Plano: {$user->plan->name}",
            ]);
        });
    }

    /**
     * Adiciona créditos extras (via compra ou bônus).
     */
    public function addCredits(User $user, int $amount, string $type = 'purchase', string $description = 'Compra de créditos'): void
    {
        $wallet = $this->getWallet($user);

        DB::transaction(function () use ($user, $wallet, $amount, $type, $description) {
            $balanceBefore = $wallet->balance;
            
            $wallet->increment('extra_credits', $amount);
            $wallet->increment('balance', $amount);
            $wallet->save();

            AiCreditTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'credits' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => $description,
            ]);
        });
    }

    /**
     * Check user balance and send notification if low or exhausted.
     */
    public function checkBalanceAndNotify(User $user): void
    {
        $balance = $this->getBalance($user);

        if ($balance <= 0) {
            $user->notify(new LowAiCreditsNotification(0, true));
            return;
        }

        // Se o saldo for menor que 20% do allowance mensal do plano (ou um mínimo de 50)
        $planCredits = $user->plan->ai_credits ?? 100;
        $threshold = max(50, floor($planCredits * 0.2));

        if ($balance < $threshold) {
            $cacheKey = "notified_low_credits_{$user->id}_{$balance}";
            if (!Cache::has($cacheKey)) {
                $user->notify(new LowAiCreditsNotification($balance));
                Cache::put($cacheKey, true, now()->addDays(1));
            }
        }
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
}
