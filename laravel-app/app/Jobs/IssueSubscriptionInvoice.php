<?php

namespace App\Jobs;

use App\Support\QueueNames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use App\Contracts\InvoiceGatewayInterface;
use Illuminate\Support\Facades\Log;

class IssueSubscriptionInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->onQueue(QueueNames::webhooks());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Se a interface não estiver bindada a uma implementação concreta, ignorar.
        if (!app()->bound(InvoiceGatewayInterface::class)) {
            Log::warning("Nenhum gateway de NFe configurado para emissão (pagamento #{$this->payment->id}).");
            return;
        }

        $gateway = app(InvoiceGatewayInterface::class);
        $user = $this->payment->user;

        if (!$user) {
            Log::error("Não é possível emitir NFe: Usuário não encontrado (pagamento #{$this->payment->id}).");
            return;
        }

        $result = $gateway->issueInvoice($this->payment, $user);

        if ($result['ok']) {
            Log::info("NFe solicitada com sucesso para pagamento #{$this->payment->id}. Invoice ID: {$result['invoice_id']}");
        } else {
            Log::error("Falha ao solicitar NFe para pagamento #{$this->payment->id}. Erro: {$result['message']}");
            // Lançar exceção se quiser que o Job faça retry (depende da configuração da fila)
            // throw new \Exception("Falha na emissão de NF: " . $result['message']);
        }
    }
}
