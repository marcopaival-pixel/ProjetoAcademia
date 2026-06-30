<?php

namespace App\Services\Shop;

use App\Models\ShopOrder;
use App\Models\ShopPointsTransaction;
use App\Models\ShopPointsWallet;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShopPointsService
{
    public function pointsPerReal(): int
    {
        $configured = SystemSetting::get('shop_points_per_real');

        return max(1, (int) ($configured ?: 100));
    }

    public function pointsRequiredForAmount(float $amount): int
    {
        return (int) ceil($amount * $this->pointsPerReal());
    }

    public function getOrCreateWallet(User $user): ShopPointsWallet
    {
        if (empty($user->academy_company_id)) {
            throw new \RuntimeException('Utilizador sem vínculo com organização para usar pontos.');
        }

        return ShopPointsWallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'academy_company_id' => $user->academy_company_id,
                'balance_points' => 0,
                'balance_cashback' => 0,
            ]
        );
    }

    public function cashbackPercent(): float
    {
        $configured = SystemSetting::get('shop_cashback_percent');

        return max(0.0, (float) ($configured !== null && $configured !== '' ? $configured : 5));
    }

    public function awardCashbackForOrder(ShopOrder $order): void
    {
        if ($order->payment_method === 'points') {
            return;
        }

        if ((int) $order->points_earned > 0) {
            return;
        }

        $percent = $this->cashbackPercent();
        if ($percent <= 0) {
            return;
        }

        $user = $order->user;
        if ($user === null) {
            return;
        }

        $points = (int) floor((float) $order->total * $this->pointsPerReal() * ($percent / 100));
        if ($points <= 0) {
            return;
        }

        $cashbackAmount = round((float) $order->total * ($percent / 100), 2);

        DB::transaction(function () use ($user, $order, $points, $cashbackAmount) {
            $this->credit(
                $user,
                $points,
                'Cashback pedido '.$order->order_number,
                'order_cashback',
                $order->id
            );

            $order->update([
                'points_earned' => $points,
                'cashback_amount' => $cashbackAmount,
            ]);
        });
    }

    public function clawbackOrderCashback(ShopOrder $order): void
    {
        $points = (int) $order->points_earned;
        if ($points <= 0) {
            return;
        }

        $user = $order->user;
        if ($user === null) {
            return;
        }

        $alreadyClawed = ShopPointsTransaction::query()
            ->where('source', 'order_cashback_clawback')
            ->where('source_id', $order->id)
            ->exists();

        if ($alreadyClawed) {
            return;
        }

        DB::transaction(function () use ($user, $order, $points) {
            $wallet = $this->getOrCreateWallet($user);
            $deduct = min($points, (int) $wallet->balance_points);

            if ($deduct > 0) {
                $wallet->decrement('balance_points', $deduct);

                ShopPointsTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $user->id,
                    'type' => ShopPointsTransaction::TYPE_REDEEM,
                    'points' => -$deduct,
                    'description' => 'Estorno cashback pedido '.$order->order_number,
                    'source' => 'order_cashback_clawback',
                    'source_id' => $order->id,
                ]);
            }
        });
    }

    public function credit(User $user, int $points, string $description, ?string $source = null, ?int $sourceId = null): ShopPointsWallet
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('Pontos devem ser positivos.');
        }

        return DB::transaction(function () use ($user, $points, $description, $source, $sourceId) {
            $wallet = $this->getOrCreateWallet($user);
            $wallet->increment('balance_points', $points);
            $wallet->increment('lifetime_points_earned', $points);

            ShopPointsTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => ShopPointsTransaction::TYPE_EARN,
                'points' => $points,
                'description' => $description,
                'source' => $source,
                'source_id' => $sourceId,
            ]);

            return $wallet->fresh();
        });
    }

    public function payOrderWithPoints(User $user, ShopOrder $order, ShopOrderService $orderService): void
    {
        $required = $this->pointsRequiredForAmount((float) $order->total);

        DB::transaction(function () use ($user, $order, $required, $orderService) {
            $wallet = $this->getOrCreateWallet($user);

            if ($wallet->balance_points < $required) {
                throw new \RuntimeException('Saldo de pontos insuficiente para este pedido.');
            }

            $wallet->decrement('balance_points', $required);

            ShopPointsTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => ShopPointsTransaction::TYPE_REDEEM,
                'points' => -$required,
                'description' => 'Resgate pedido '.$order->order_number,
                'source' => 'order',
                'source_id' => $order->id,
            ]);

            $order->update([
                'payment_method' => 'points',
                'payment_gateway' => 'points',
            ]);

            $orderService->markAsPaid($order, 'points-'.$order->id, 'points');
            $order->update(['points_earned' => 0]);
        });
    }

    public function refundOrderRedemption(ShopOrder $order): void
    {
        $user = $order->user;

        if ($user === null || $order->payment_method !== 'points') {
            return;
        }

        $redeem = ShopPointsTransaction::query()
            ->where('user_id', $user->id)
            ->where('source', 'order')
            ->where('source_id', $order->id)
            ->where('type', ShopPointsTransaction::TYPE_REDEEM)
            ->first();

        if ($redeem === null) {
            return;
        }

        $alreadyRefunded = ShopPointsTransaction::query()
            ->where('source', 'order')
            ->where('source_id', $order->id)
            ->where('type', ShopPointsTransaction::TYPE_REFUND)
            ->exists();

        if ($alreadyRefunded) {
            return;
        }

        $points = abs((int) $redeem->points);

        DB::transaction(function () use ($user, $order, $points, $redeem) {
            $wallet = $this->getOrCreateWallet($user);
            $wallet->increment('balance_points', $points);

            ShopPointsTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => ShopPointsTransaction::TYPE_REFUND,
                'points' => $points,
                'description' => 'Estorno pedido '.$order->order_number,
                'source' => 'order',
                'source_id' => $order->id,
            ]);
        });
    }
}
