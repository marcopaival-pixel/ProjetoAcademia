<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Shop\ShopRecommendationService;
use App\Support\QueueNames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshShopRecommendationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId)
    {
        $this->onQueue(QueueNames::shop());
    }

    public function handle(ShopRecommendationService $recommendations): void
    {
        $user = User::find($this->userId);

        if ($user === null || $user->academy_company_id === null) {
            return;
        }

        $recommendations->refreshForUser($user);
    }
}
