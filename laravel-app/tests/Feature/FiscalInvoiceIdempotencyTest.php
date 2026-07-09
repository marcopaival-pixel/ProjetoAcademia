<?php

namespace Tests\Feature;

use App\Contracts\InvoiceGatewayInterface;
use App\Mail\FiscalInvoiceIssuedMail;
use App\Models\FiscalInvoice;
use App\Models\FiscalSetting;
use App\Models\Payment;
use App\Models\User;
use App\Services\FiscalInvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FiscalInvoiceIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_fiscal_invoice_is_not_duplicated_for_same_payment(): void
    {
        Mail::fake();
        $this->createFiscalSetting();
        $user = $this->createFiscalUser();
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'mp-idem-001',
            'amount' => 99.90,
            'fee_amount' => 0,
            'net_amount' => 99.90,
            'currency' => 'BRL',
            'status' => 'paid',
            'payload' => [],
        ]);

        $gateway = new class implements InvoiceGatewayInterface
        {
            public int $calls = 0;

            public function issueInvoice(Payment $payment, User $user): array
            {
                $this->calls++;

                return [
                    'ok' => true,
                    'invoice_id' => 'nfse-001',
                    'invoice_number' => '1001',
                    'verification_code' => 'ABC123',
                    'official_url' => 'https://notas.example.test/1001',
                    'pdf_url' => 'https://notas.example.test/1001.pdf',
                    'xml_url' => 'https://notas.example.test/1001.xml',
                    'message' => 'ok',
                ];
            }

            public function cancelInvoice(string $invoiceId, string $reason): array
            {
                return ['ok' => true, 'message' => 'cancelled'];
            }

            public function getInvoiceStatus(string $invoiceId): string
            {
                return 'issued';
            }
        };

        $this->app->instance(InvoiceGatewayInterface::class, $gateway);

        $service = app(FiscalInvoiceService::class);
        $first = $service->issueForPayment($payment, 'test');
        $second = $service->issueForPayment($payment, 'test');

        $this->assertSame(1, $gateway->calls);
        $this->assertSame($first->id, $second->id);
        $this->assertSame(FiscalInvoice::STATUS_ISSUED, $second->status);
        $this->assertDatabaseCount('fiscal_invoices', 1);
        $this->assertDatabaseHas('fiscal_invoices', [
            'payment_id' => $payment->id,
            'status' => FiscalInvoice::STATUS_ISSUED,
            'invoice_number' => '1001',
        ]);
        $this->assertDatabaseHas('fiscal_invoice_logs', [
            'payment_id' => $payment->id,
            'event' => 'skipped_duplicate',
        ]);
        Mail::assertQueued(FiscalInvoiceIssuedMail::class);
    }

    public function test_unpaid_payment_does_not_call_invoice_gateway(): void
    {
        $this->createFiscalSetting();
        $user = $this->createFiscalUser();
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'mp-pending-001',
            'amount' => 99.90,
            'fee_amount' => 0,
            'net_amount' => 99.90,
            'currency' => 'BRL',
            'status' => 'pending',
            'payload' => [],
        ]);

        $gateway = new class implements InvoiceGatewayInterface
        {
            public int $calls = 0;

            public function issueInvoice(Payment $payment, User $user): array
            {
                $this->calls++;

                return ['ok' => true, 'invoice_id' => 'unexpected', 'message' => 'unexpected'];
            }

            public function cancelInvoice(string $invoiceId, string $reason): array
            {
                return ['ok' => true, 'message' => 'cancelled'];
            }

            public function getInvoiceStatus(string $invoiceId): string
            {
                return 'issued';
            }
        };

        $this->app->instance(InvoiceGatewayInterface::class, $gateway);

        $invoice = app(FiscalInvoiceService::class)->issueForPayment($payment, 'test');

        $this->assertSame(0, $gateway->calls);
        $this->assertSame(FiscalInvoice::STATUS_PENDING, $invoice->status);
        $this->assertDatabaseCount('fiscal_invoices', 1);
        $this->assertDatabaseHas('fiscal_invoice_logs', [
            'payment_id' => $payment->id,
            'event' => 'blocked_unpaid_payment',
        ]);
    }

    public function test_missing_fiscal_data_blocks_issue_before_gateway_call(): void
    {
        $user = User::factory()->create(['cpf' => '12345678901']);
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'mp-blocked-001',
            'amount' => 99.90,
            'fee_amount' => 0,
            'net_amount' => 99.90,
            'currency' => 'BRL',
            'status' => 'paid',
            'payload' => [],
        ]);

        $gateway = new class implements InvoiceGatewayInterface
        {
            public int $calls = 0;

            public function issueInvoice(Payment $payment, User $user): array
            {
                $this->calls++;

                return ['ok' => true, 'invoice_id' => 'unexpected', 'message' => 'unexpected'];
            }

            public function cancelInvoice(string $invoiceId, string $reason): array
            {
                return ['ok' => true, 'message' => 'cancelled'];
            }

            public function getInvoiceStatus(string $invoiceId): string
            {
                return 'issued';
            }
        };
        $this->app->instance(InvoiceGatewayInterface::class, $gateway);

        $invoice = app(FiscalInvoiceService::class)->issueForPayment($payment, 'test');

        $this->assertSame(0, $gateway->calls);
        $this->assertSame(FiscalInvoice::STATUS_BLOCKED, $invoice->status);
        $this->assertDatabaseHas('fiscal_invoice_logs', [
            'payment_id' => $payment->id,
            'event' => 'blocked_invalid_fiscal_data',
        ]);
    }

    public function test_issued_invoice_can_be_cancelled_once(): void
    {
        Mail::fake();
        $this->createFiscalSetting();
        $user = $this->createFiscalUser();
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'mp-cancel-001',
            'amount' => 99.90,
            'fee_amount' => 0,
            'net_amount' => 99.90,
            'currency' => 'BRL',
            'status' => 'paid',
            'payload' => [],
        ]);

        $gateway = new class implements InvoiceGatewayInterface
        {
            public int $issueCalls = 0;

            public int $cancelCalls = 0;

            public function issueInvoice(Payment $payment, User $user): array
            {
                $this->issueCalls++;

                return ['ok' => true, 'invoice_id' => 'nfse-cancel-001', 'invoice_number' => '2001', 'message' => 'ok'];
            }

            public function cancelInvoice(string $invoiceId, string $reason): array
            {
                $this->cancelCalls++;

                return ['ok' => true, 'message' => 'cancelled'];
            }

            public function getInvoiceStatus(string $invoiceId): string
            {
                return 'issued';
            }
        };
        $this->app->instance(InvoiceGatewayInterface::class, $gateway);

        $service = app(FiscalInvoiceService::class);
        $invoice = $service->issueForPayment($payment, 'test');
        $cancelled = $service->cancel($invoice, 'Estorno homologacao', 1);
        $again = $service->cancel($cancelled, 'Estorno homologacao', 1);

        $this->assertSame(1, $gateway->issueCalls);
        $this->assertSame(1, $gateway->cancelCalls);
        $this->assertSame(FiscalInvoice::STATUS_CANCELLED, $again->status);
        $this->assertDatabaseHas('fiscal_invoice_logs', [
            'payment_id' => $payment->id,
            'event' => 'cancel_skipped_duplicate',
        ]);
    }

    private function createFiscalSetting(): FiscalSetting
    {
        return FiscalSetting::create([
            'provider' => 'fake',
            'environment' => 'sandbox',
            'issuer_cnpj' => '12345678000190',
            'issuer_legal_name' => 'NexShape Tecnologia Ltda',
            'municipal_registration' => '12345',
            'tax_regime' => 'Simples Nacional',
            'cnae' => '6201501',
            'municipal_service_code' => '1.05',
            'iss_rate' => 2.00,
            'is_active' => true,
        ]);
    }

    private function createFiscalUser(): User
    {
        return User::factory()->create([
            'cpf' => '12345678901',
            'fiscal_name' => 'Cliente Fiscal',
            'fiscal_document_type' => 'CPF',
            'fiscal_document' => '12345678901',
            'fiscal_email' => 'cliente@example.test',
            'fiscal_address' => 'Rua Teste, 123',
            'fiscal_city' => 'Sao Paulo',
            'fiscal_state' => 'SP',
            'fiscal_zip_code' => '01001000',
        ]);
    }
}
