<?php

namespace App\Services;

use App\Contracts\InvoiceGatewayInterface;
use App\Mail\FiscalInvoiceIssuedMail;
use App\Models\FiscalInvoice;
use App\Models\FiscalInvoiceLog;
use App\Models\FiscalSetting;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class FiscalInvoiceService
{
    public function issueForPayment(Payment $payment, string $origin = 'system'): FiscalInvoice
    {
        return DB::transaction(function () use ($payment, $origin) {
            $payment = Payment::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $invoice = FiscalInvoice::query()
                ->where('payment_id', $payment->id)
                ->lockForUpdate()
                ->first();

            if (! $invoice) {
                $invoice = FiscalInvoice::create($this->initialInvoiceData($payment));
                $this->log($invoice, 'created', null, $invoice->status, 'Registro fiscal criado.', ['origin' => $origin]);
            }

            if (in_array($invoice->status, [
                FiscalInvoice::STATUS_ISSUED,
                FiscalInvoice::STATUS_PROCESSING,
                FiscalInvoice::STATUS_CANCEL_PENDING,
                FiscalInvoice::STATUS_CANCELLED,
            ], true)) {
                $this->log($invoice, 'skipped_duplicate', $invoice->status, $invoice->status, 'Emissão ignorada por status fiscal idempotente.', ['origin' => $origin]);

                return $invoice;
            }

            if (! $this->isPaid($payment)) {
                $before = $invoice->status;
                $invoice->update([
                    'status' => FiscalInvoice::STATUS_PENDING,
                    'last_error' => 'Pagamento ainda não aprovado para emissão fiscal.',
                ]);
                $this->log($invoice, 'blocked_unpaid_payment', $before, $invoice->status, $invoice->last_error, ['origin' => $origin]);

                return $invoice;
            }

            if (! $payment->user) {
                $before = $invoice->status;
                $invoice->update([
                    'status' => FiscalInvoice::STATUS_FAILED,
                    'attempts' => $invoice->attempts + 1,
                    'last_attempt_at' => now(),
                    'last_error' => 'Cliente do pagamento não encontrado.',
                ]);
                $this->log($invoice, 'customer_missing', $before, $invoice->status, $invoice->last_error, ['origin' => $origin]);

                return $invoice;
            }

            $setting = FiscalSetting::active();
            $validationErrors = $this->validateFiscalData($payment, $setting);
            if ($validationErrors !== []) {
                $before = $invoice->status;
                $invoice->update([
                    'status' => FiscalInvoice::STATUS_BLOCKED,
                    'last_error' => 'Dados fiscais incompletos: '.implode('; ', $validationErrors),
                ]);
                $this->log($invoice, 'blocked_invalid_fiscal_data', $before, $invoice->status, $invoice->last_error, [
                    'origin' => $origin,
                    'errors' => $validationErrors,
                ]);

                return $invoice;
            }

            try {
                $gateway = app(InvoiceGatewayInterface::class);
            } catch (\Throwable $e) {
                $before = $invoice->status;
                $invoice->update([
                    'status' => FiscalInvoice::STATUS_FAILED,
                    'attempts' => $invoice->attempts + 1,
                    'last_attempt_at' => now(),
                    'last_error' => 'Nenhum gateway fiscal configurado: '.$e->getMessage(),
                ]);
                $this->log($invoice, 'gateway_missing', $before, $invoice->status, $invoice->last_error, ['origin' => $origin]);
                Log::warning("[FiscalInvoice] Nenhum gateway fiscal configurado para pagamento #{$payment->id}.");

                return $invoice;
            }

            $before = $invoice->status;
            $invoice->update([
                'status' => FiscalInvoice::STATUS_PROCESSING,
                'attempts' => $invoice->attempts + 1,
                'last_attempt_at' => now(),
                'last_error' => null,
            ]);
            $this->log($invoice, 'issue_attempt', $before, $invoice->status, 'Tentativa de emissão fiscal iniciada.', ['origin' => $origin]);

            $result = $gateway->issueInvoice($payment, $payment->user);
            $ok = (bool) ($result['ok'] ?? false);

            $before = $invoice->status;
            if ($ok) {
                $invoice->update([
                    'status' => FiscalInvoice::STATUS_ISSUED,
                    'provider' => $result['provider'] ?? $setting?->provider,
                    'provider_invoice_id' => $result['invoice_id'] ?? null,
                    'invoice_number' => $result['invoice_number'] ?? null,
                    'verification_code' => $result['verification_code'] ?? null,
                    'official_url' => $result['official_url'] ?? null,
                    'pdf_url' => $result['pdf_url'] ?? null,
                    'xml_url' => $result['xml_url'] ?? null,
                    'issued_at' => now(),
                    'response_payload' => $result,
                ]);
                $this->log($invoice, 'issued', $before, $invoice->status, 'Nota fiscal emitida.', ['origin' => $origin, 'result' => $result]);
                $this->sendIssuedEmail($invoice->fresh(['user', 'payment']));
            } else {
                $invoice->update([
                    'status' => FiscalInvoice::STATUS_REJECTED,
                    'last_error' => (string) ($result['message'] ?? 'Gateway fiscal rejeitou a emissão.'),
                    'response_payload' => $result,
                ]);
                $this->log($invoice, 'rejected', $before, $invoice->status, $invoice->last_error, ['origin' => $origin, 'result' => $result]);
            }

            return $invoice->fresh();
        });
    }

    public function reprocess(FiscalInvoice $invoice, ?int $actorId = null): FiscalInvoice
    {
        if ($invoice->status === FiscalInvoice::STATUS_ISSUED) {
            $this->log($invoice, 'manual_reprocess_skipped', $invoice->status, $invoice->status, 'Nota já emitida; reprocessamento bloqueado.', ['actor_id' => $actorId]);

            return $invoice;
        }

        $this->log($invoice, 'manual_reprocess_requested', $invoice->status, FiscalInvoice::STATUS_PENDING, 'Reprocessamento manual solicitado.', ['actor_id' => $actorId]);
        $invoice->update(['status' => FiscalInvoice::STATUS_PENDING, 'last_error' => null]);

        return $this->issueForPayment($invoice->payment, 'manual');
    }

    public function cancel(FiscalInvoice $invoice, string $reason, ?int $actorId = null): FiscalInvoice
    {
        return DB::transaction(function () use ($invoice, $reason, $actorId) {
            $invoice = FiscalInvoice::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if ($invoice->status === FiscalInvoice::STATUS_CANCELLED) {
                $this->log($invoice, 'cancel_skipped_duplicate', $invoice->status, $invoice->status, 'Nota já cancelada.', ['actor_id' => $actorId]);

                return $invoice;
            }

            if ($invoice->status !== FiscalInvoice::STATUS_ISSUED || ! $invoice->provider_invoice_id) {
                $this->log($invoice, 'cancel_blocked', $invoice->status, $invoice->status, 'Cancelamento permitido apenas para nota emitida com ID do provedor.', [
                    'actor_id' => $actorId,
                    'reason' => $reason,
                ]);

                return $invoice;
            }

            $before = $invoice->status;
            $invoice->update(['status' => FiscalInvoice::STATUS_CANCEL_PENDING]);
            $this->log($invoice, 'cancel_attempt', $before, $invoice->status, 'Tentativa de cancelamento fiscal iniciada.', [
                'actor_id' => $actorId,
                'reason' => $reason,
            ]);

            try {
                $result = app(InvoiceGatewayInterface::class)->cancelInvoice($invoice->provider_invoice_id, $reason);
            } catch (\Throwable $e) {
                $result = ['ok' => false, 'message' => 'Nenhum gateway fiscal configurado: '.$e->getMessage()];
            }
            $ok = (bool) ($result['ok'] ?? false);

            $before = $invoice->status;
            if ($ok) {
                $invoice->update([
                    'status' => FiscalInvoice::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                    'last_error' => null,
                    'response_payload' => array_merge($invoice->response_payload ?? [], ['cancel' => $result]),
                ]);
                $this->log($invoice, 'cancelled', $before, $invoice->status, 'Nota fiscal cancelada.', [
                    'actor_id' => $actorId,
                    'reason' => $reason,
                    'result' => $result,
                ]);
            } else {
                $invoice->update([
                    'status' => FiscalInvoice::STATUS_FAILED,
                    'last_error' => (string) ($result['message'] ?? 'Gateway fiscal rejeitou o cancelamento.'),
                    'response_payload' => array_merge($invoice->response_payload ?? [], ['cancel' => $result]),
                ]);
                $this->log($invoice, 'cancel_failed', $before, $invoice->status, $invoice->last_error, [
                    'actor_id' => $actorId,
                    'reason' => $reason,
                    'result' => $result,
                ]);
            }

            return $invoice->fresh();
        });
    }

    private function initialInvoiceData(Payment $payment): array
    {
        $payload = $payment->payload ?? [];
        $gross = (float) $payment->amount;
        $net = (float) ($payment->net_amount ?: $payment->amount);
        $discount = max(0, round($gross - $net, 2));
        $setting = FiscalSetting::active();

        return [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'academy_company_id' => $payment->academy_company_id,
            'status' => FiscalInvoice::STATUS_PENDING,
            'provider' => $setting?->provider,
            'gross_amount' => $gross,
            'discount_amount' => $discount,
            'net_amount' => $net,
            'iss_rate' => $setting?->iss_rate,
            'iss_amount' => $this->issAmount($net, $setting?->iss_rate),
            'service_code' => $setting?->municipal_service_code,
            'service_description' => $this->serviceDescription($payment),
            'request_payload' => [
                'payment_id' => $payment->id,
                'gateway' => $payment->gateway,
                'gateway_id' => $payment->gateway_id,
                'payload_reference' => $payload['external_reference'] ?? ($payload['metadata']['plan'] ?? null),
            ],
        ];
    }

    private function serviceDescription(Payment $payment): string
    {
        $planName = $payment->subscription?->plan?->name;

        return $planName
            ? "Plano {$planName} NexShape - mensalidade de ".now()->translatedFormat('F/Y')
            : "Serviço NexShape vinculado ao pagamento {$payment->gateway_id}";
    }

    private function isPaid(Payment $payment): bool
    {
        return in_array($payment->status, [
            'paid',
            'approved',
            Subscription::STATUS_FIN_ATIVO,
        ], true);
    }

    /**
     * @return array<int, string>
     */
    private function validateFiscalData(Payment $payment, ?FiscalSetting $setting): array
    {
        $errors = [];
        $user = $payment->user;

        if (! $setting || ! $setting->is_active) {
            $errors[] = 'configuração fiscal ativa ausente';
        } else {
            foreach ([
                'issuer_cnpj' => 'CNPJ do emitente',
                'issuer_legal_name' => 'razão social do emitente',
                'municipal_registration' => 'inscrição municipal',
                'tax_regime' => 'regime tributário',
                'cnae' => 'CNAE',
                'municipal_service_code' => 'código de serviço municipal',
                'iss_rate' => 'alíquota de ISS',
            ] as $field => $label) {
                if (blank($setting->{$field})) {
                    $errors[] = $label;
                }
            }
        }

        if (! $user) {
            $errors[] = 'cliente';

            return $errors;
        }

        $document = preg_replace('/\D+/', '', (string) ($user->fiscal_document ?: $user->cnpj ?: $user->cpf));
        $documentType = $user->fiscal_document_type ?: (strlen($document) === 14 ? 'CNPJ' : 'CPF');
        $required = [
            'nome/razão social do cliente' => $user->fiscal_name ?: $user->name,
            'CPF/CNPJ do cliente' => $document,
            'e-mail do cliente' => $user->fiscal_email ?: $user->email,
            'endereço fiscal do cliente' => $user->fiscal_address,
            'cidade fiscal do cliente' => $user->fiscal_city,
            'UF fiscal do cliente' => $user->fiscal_state,
            'CEP fiscal do cliente' => $user->fiscal_zip_code,
        ];

        foreach ($required as $label => $value) {
            if (blank($value)) {
                $errors[] = $label;
            }
        }

        if ($document !== '' && ! in_array(strlen($document), [11, 14], true)) {
            $errors[] = 'CPF/CNPJ do cliente inválido';
        }

        if ($documentType === 'CPF' && strlen($document) !== 11) {
            $errors[] = 'CPF do cliente deve ter 11 dígitos';
        }

        if ($documentType === 'CNPJ' && strlen($document) !== 14) {
            $errors[] = 'CNPJ do cliente deve ter 14 dígitos';
        }

        $zip = preg_replace('/\D+/', '', (string) $user->fiscal_zip_code);
        if ($zip !== '' && strlen($zip) !== 8) {
            $errors[] = 'CEP fiscal do cliente inválido';
        }

        if ($user->fiscal_state && strlen($user->fiscal_state) !== 2) {
            $errors[] = 'UF fiscal do cliente inválida';
        }

        return array_values(array_unique($errors));
    }

    private function issAmount(float $net, mixed $rate): ?float
    {
        if ($rate === null || $rate === '') {
            return null;
        }

        return round($net * ((float) $rate / 100), 2);
    }

    private function sendIssuedEmail(FiscalInvoice $invoice): void
    {
        $email = $invoice->user?->fiscal_email ?: $invoice->user?->email;

        if (! $email) {
            $this->log($invoice, 'email_skipped', $invoice->status, $invoice->status, 'Cliente sem e-mail fiscal.');

            return;
        }

        try {
            Mail::to($email)->queue(new FiscalInvoiceIssuedMail($invoice));
            $this->log($invoice, 'email_queued', $invoice->status, $invoice->status, 'E-mail de nota fiscal enfileirado.', ['email' => $email]);
        } catch (\Throwable $e) {
            $this->log($invoice, 'email_failed', $invoice->status, $invoice->status, $e->getMessage(), ['email' => $email]);
        }
    }

    private function log(FiscalInvoice $invoice, string $event, ?string $before, ?string $after, ?string $message = null, ?array $payload = null): void
    {
        FiscalInvoiceLog::create([
            'fiscal_invoice_id' => $invoice->id,
            'payment_id' => $invoice->payment_id,
            'user_id' => $invoice->user_id,
            'event' => $event,
            'status_before' => $before,
            'status_after' => $after,
            'message' => $message,
            'payload' => $payload,
            'ip_address' => Request::ip(),
        ]);
    }
}
