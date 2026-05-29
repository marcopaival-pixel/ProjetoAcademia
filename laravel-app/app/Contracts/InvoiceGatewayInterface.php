<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Payment;

interface InvoiceGatewayInterface
{
    /**
     * Emite a nota fiscal (NFe ou NFS-e) baseada no pagamento aprovado.
     * 
     * @param Payment $payment Pagamento recebido
     * @param User $user Cliente vinculado
     * @return array{ok: bool, invoice_id: string|null, message: string}
     */
    public function issueInvoice(Payment $payment, User $user): array;

    /**
     * Cancela uma nota fiscal previamente emitida.
     * 
     * @param string $invoiceId ID da nota no gateway de NF
     * @param string $reason Motivo do cancelamento
     * @return array{ok: bool, message: string}
     */
    public function cancelInvoice(string $invoiceId, string $reason): array;

    /**
     * Consulta o status atual de uma nota fiscal.
     * 
     * @param string $invoiceId ID da nota no gateway de NF
     * @return string Status (ex: 'issued', 'processing', 'failed', 'cancelled')
     */
    public function getInvoiceStatus(string $invoiceId): string;
}
