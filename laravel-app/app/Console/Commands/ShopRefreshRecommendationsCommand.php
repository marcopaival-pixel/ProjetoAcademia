<?php

namespace App\Console\Commands;

use App\Jobs\RefreshShopRecommendationsJob;
use App\Models\ShopRecommendation;
use App\Models\User;
use Illuminate\Console\Command;

class ShopRefreshRecommendationsCommand extends Command
{
    protected $signature = 'app:shop:refresh-recommendations
                            {--user= : ID do utilizador (opcional)}
                            {--all : Recalcular para todos os alunos com academy_company_id}
                            {--sync : Executar inline sem fila}';

    protected $description = 'Recalcula recomendações do shopping (cache shop_recommendations).';

    public function handle(): int
    {
        $userId = $this->option('user');

        if ($userId !== null && $userId !== '') {
            $this->dispatchForUser((int) $userId);

            return self::SUCCESS;
        }

        $userIds = ShopRecommendation::query()
            ->where('expires_at', '<=', now())
            ->pluck('user_id');

        if ($this->option('all')) {
            $userIds = $userIds->merge(
                User::query()
                    ->whereNotNull('academy_company_id')
                    ->where('status', 'active')
                    ->pluck('id')
            );
        }

        $userIds = $userIds->unique()->values();

        $count = 0;
        foreach ($userIds as $id) {
            $this->dispatchForUser((int) $id);
            $count++;
        }

        $this->info("Recomendações enfileiradas/recalculadas para {$count} utilizador(es).");

        return self::SUCCESS;
    }

    private function dispatchForUser(int $userId): void
    {
        if ($this->option('sync')) {
            RefreshShopRecommendationsJob::dispatchSync($userId);
            $this->line("  [ok] user_id={$userId}");
        } else {
            RefreshShopRecommendationsJob::dispatch($userId);
        }
    }
}
