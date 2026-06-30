<?php

namespace App\Jobs;

use App\Services\Lgpd\LgpdDeletionWorkflowService;
use App\Support\QueueNames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLgpdDeletionRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public ?int $actorId = null,
    ) {
        $this->onQueue(QueueNames::default());
    }

    public function handle(LgpdDeletionWorkflowService $workflow): void
    {
        $actor = $this->actorId ? \App\Models\User::find($this->actorId) : null;

        $workflow->processUserById(
            $this->userId,
            $actor,
            'Processamento automático LGPD (fila)'
        );
    }
}
