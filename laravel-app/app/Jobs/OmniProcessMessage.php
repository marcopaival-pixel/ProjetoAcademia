<?php

namespace App\Jobs;

use App\Models\OmniConversation;
use App\Services\OmniChatService;
use App\Support\QueueNames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OmniProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $conversation;
    protected $content;

    /**
     * Create a new job instance.
     */
    public function __construct(OmniConversation $conversation, string $content)
    {
        $this->conversation = $conversation;
        $this->content = $content;
        $this->onQueue(QueueNames::webhooks());
    }

    /**
     * Execute the job.
     */
    public function handle(OmniChatService $service): void
    {
        $service->replyToMessage($this->conversation, $this->content);
    }
}
