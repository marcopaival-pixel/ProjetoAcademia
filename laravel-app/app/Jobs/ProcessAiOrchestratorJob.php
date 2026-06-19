<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AI\OrchestratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessAiOrchestratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(
        public int $userId,
        public string $message,
        public array $context,
        public string $resultCacheKey,
    ) {
        $this->onQueue(config('ai.queue_name', 'ai'));
    }

    public function handle(OrchestratorService $orchestrator): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            Cache::put($this->resultCacheKey, ['status' => 'error', 'error' => 'Usuário não encontrado'], 300);

            return;
        }

        $result = $orchestrator->run($user, $this->message, $this->context);
        Cache::put($this->resultCacheKey, $result, 600);
    }
}
