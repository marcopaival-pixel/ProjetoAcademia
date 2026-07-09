<?php

namespace App\Services\Fiscal;

use App\Contracts\InvoiceGatewayInterface;
use App\Models\Payment;
use App\Models\User;

class FakeInvoiceGateway implements InvoiceGatewayInterface
{
    public function issueInvoice(Payment $payment, User $user): array
    {
        $number = 'HOM-'.str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT);
        $verification = strtoupper(substr(hash('sha256', $payment->gateway.'|'.$payment->gateway_id), 0, 12));

        return [
            'ok' => true,
            'invoice_id' => 'fake-'.$payment->id,
            'invoice_number' => $number,
            'verification_code' => $verification,
            'official_url' => url('/admin/financial/fiscal-invoices?search='.$number),
            'pdf_url' => url('/admin/financial/fiscal-invoices?search='.$number.'&format=pdf'),
            'xml_url' => url('/admin/financial/fiscal-invoices?search='.$number.'&format=xml'),
            'message' => 'Nota fiscal simulada para homologação interna.',
            'provider' => 'fake',
        ];
    }

    public function cancelInvoice(string $invoiceId, string $reason): array
    {
        return [
            'ok' => true,
            'message' => 'Cancelamento fiscal simulado para homologação interna.',
        ];
    }

    public function getInvoiceStatus(string $invoiceId): string
    {
        return 'issued';
    }
}
