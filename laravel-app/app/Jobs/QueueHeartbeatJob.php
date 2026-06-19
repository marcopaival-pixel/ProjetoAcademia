<?php

namespace App\Jobs;

use App\Support\QueueNames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QueueHeartbeatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue(QueueNames::default());
    }

    public function handle(): void
    {
        $timestamp = now()->toIso8601String();

        Cache::put('operations.queue_heartbeat_at', $timestamp, now()->addMinutes(10));
        Log::debug('Queue heartbeat processed.', ['processed_at' => $timestamp]);
    }
}
