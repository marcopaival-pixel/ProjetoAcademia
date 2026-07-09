<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\FiscalInvoiceService;
use App\Support\QueueNames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssueSubscriptionInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Payment $payment)
    {
        $this->onQueue(QueueNames::webhooks());
    }

    public function handle(): void
    {
        app(FiscalInvoiceService::class)->issueForPayment($this->payment, 'job');
    }
}
