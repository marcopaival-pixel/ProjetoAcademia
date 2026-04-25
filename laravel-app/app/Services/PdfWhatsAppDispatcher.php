<?php

namespace App\Services;

use App\Enums\PdfDeliveryChannel;
use App\Models\HistoricoPdf;
use App\Models\PdfDeliveryLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Integração WhatsApp: preparada para API HTTP configurável (ex.: Meta Cloud API).
 * Sem credenciais válidas, regista entrega como "skipped" com motivo.
 */
class PdfWhatsAppDispatcher
{
    public function dispatchForHistorico(HistoricoPdf $historico, string $disk, string $path): void
    {
        $template = $historico->template;
        if ($template === null) {
            return;
        }

        $phones = $historico->metadata['whatsapp_recipients'] ?? null;
        if (! is_array($phones) || $phones === []) {
            $phones = is_array($template->auto_whatsapp_recipients) ? $template->auto_whatsapp_recipients : [];
        }

        $msg = $template->whatsapp_message_template
            ?? 'O seu documento foi gerado com sucesso.';

        $driver = (string) config('services.whatsapp.driver', 'none');

        foreach ($phones as $raw) {
            $phone = is_string($raw) ? preg_replace('/\D+/', '', $raw) : '';
            if ($phone === '') {
                continue;
            }

            $log = PdfDeliveryLog::create([
                'historico_pdf_id' => $historico->id,
                'channel' => PdfDeliveryChannel::Whatsapp,
                'telefone_destinatario' => $phone,
                'status_envio' => 'pending',
                'tentativas' => 0,
            ]);

            if ($driver === 'none' || $driver === '') {
                $log->update([
                    'status_envio' => 'skipped',
                    'ultimo_erro' => 'WhatsApp não configurado (services.whatsapp.driver).',
                    'tentativas' => 1,
                ]);

                continue;
            }

            try {
                $this->sendViaHttp($historico, $disk, $path, $phone, $msg);
                $log->update([
                    'status_envio' => 'sent',
                    'data_envio' => now(),
                    'tentativas' => 1,
                ]);
            } catch (\Throwable $e) {
                $log->update([
                    'status_envio' => 'failed',
                    'tentativas' => 1,
                    'ultimo_erro' => Str::limit($e->getMessage(), 2000),
                ]);
            }
        }

        if ($phones === []) {
            PdfDeliveryLog::create([
                'historico_pdf_id' => $historico->id,
                'channel' => PdfDeliveryChannel::Whatsapp,
                'telefone_destinatario' => null,
                'status_envio' => 'skipped',
                'tentativas' => 0,
                'ultimo_erro' => 'Nenhum telefone destino configurado no modelo ou metadados do documento.',
            ]);
        }
    }

    private function sendViaHttp(HistoricoPdf $historico, string $disk, string $path, string $phone, string $message): void
    {
        $url = (string) config('services.whatsapp.api_url', '');
        $token = (string) config('services.whatsapp.token', '');
        if ($url === '') {
            throw new \RuntimeException('WHATSAPP_API_URL não definido.');
        }

        $absolute = Storage::disk($disk)->path($path);
        if (! is_readable($absolute)) {
            throw new \RuntimeException('Ficheiro PDF inacessível para envio.');
        }

        $payload = [
            'to' => $phone,
            'message' => $message,
            'document_reference' => $historico->codigo_validacao,
        ];

        $headers = ['Accept' => 'application/json'];
        if ($token !== '') {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        Http::withHeaders($headers)
            ->timeout(30)
            ->attach('document', file_get_contents($absolute), $historico->nome_arquivo)
            ->post($url, $payload)
            ->throw();
    }
}
