<?php

namespace App\Jobs;

use App\Enums\PdfDeliveryChannel;
use App\Mail\HistoricoPdfMail;
use App\Models\HistoricoPdf;
use App\Models\PdfDeliveryLog;
use App\Models\User;
use App\Services\PdfWhatsAppDispatcher;
use App\Services\TransactionalMailService;
use App\Support\MailSendType;
use App\Support\QueueNames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SendPdfDocumentDeliveriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $historicoPdfId
    ) {
        $this->onQueue(QueueNames::pdf());
    }

    public function handle(PdfWhatsAppDispatcher $whatsApp): void
    {
        $historico = HistoricoPdf::query()->with(['template', 'company', 'user'])->find($this->historicoPdfId);
        if ($historico === null || $historico->template === null) {
            return;
        }

        $template = $historico->template;
        $disk = config('pdf.historico_disk', 'local');
        $path = $historico->caminho_arquivo;

        if (! Storage::disk($disk)->exists($path)) {
            return;
        }

        $ccEmails = [];
        if (is_array($template->auto_email_recipients)) {
            foreach ($template->auto_email_recipients as $email) {
                $email = is_string($email) ? trim($email) : '';
                if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $ccEmails[] = $email;
            }
        }

        if ($template->auto_email_enabled) {
            if ($historico->user_id !== null) {
                $log = PdfDeliveryLog::create([
                    'historico_pdf_id' => $historico->id,
                    'channel' => PdfDeliveryChannel::Email,
                    'email_destinatario' => null,
                    'status_envio' => 'pending',
                    'tentativas' => 0,
                ]);
                try {
                    $owner = $historico->user ?? User::query()->find($historico->user_id);
                    $resolved = $owner !== null ? trim((string) $owner->email) : '';
                    if ($owner !== null) {
                        $log->update(['email_destinatario' => $resolved !== '' ? $resolved : null]);
                    }

                    $ok = app(TransactionalMailService::class)->sendToUser(
                        new HistoricoPdfMail($historico, $disk, $path),
                        (int) $historico->user_id,
                        $historico->academy_company_id,
                        MailSendType::PDF,
                        'Documento PDF #'.$historico->id,
                        'Envio automático de histórico #'.$historico->id,
                        $ccEmails,
                        []
                    );
                    if ($ok) {
                        $log->update([
                            'status_envio' => 'sent',
                            'data_envio' => now(),
                            'tentativas' => $log->tentativas + 1,
                        ]);
                    } else {
                        $log->update([
                            'status_envio' => 'failed',
                            'tentativas' => $log->tentativas + 1,
                            'ultimo_erro' => 'Envio falhou (ver log_envio_email ou limites).',
                        ]);
                    }
                } catch (\Throwable $e) {
                    $log->update([
                        'status_envio' => 'failed',
                        'tentativas' => $log->tentativas + 1,
                        'ultimo_erro' => Str::limit($e->getMessage(), 2000),
                    ]);
                }
            } elseif ($ccEmails !== []) {
                foreach ($ccEmails as $email) {
                    $log = PdfDeliveryLog::create([
                        'historico_pdf_id' => $historico->id,
                        'channel' => PdfDeliveryChannel::Email,
                        'email_destinatario' => $email,
                        'status_envio' => 'pending',
                        'tentativas' => 0,
                    ]);
                    try {
                        $ok = app(TransactionalMailService::class)->send(
                            new HistoricoPdfMail($historico, $disk, $path),
                            $email,
                            $historico->academy_company_id,
                            null,
                            'Documento PDF #'.$historico->id,
                            'Envio automático de histórico #'.$historico->id,
                            MailSendType::PDF
                        );
                        if ($ok) {
                            $log->update([
                                'status_envio' => 'sent',
                                'data_envio' => now(),
                                'tentativas' => $log->tentativas + 1,
                            ]);
                        } else {
                            $log->update([
                                'status_envio' => 'failed',
                                'tentativas' => $log->tentativas + 1,
                                'ultimo_erro' => 'Envio falhou (ver log_envio_email ou limites).',
                            ]);
                        }
                    } catch (\Throwable $e) {
                        $log->update([
                            'status_envio' => 'failed',
                            'tentativas' => $log->tentativas + 1,
                            'ultimo_erro' => Str::limit($e->getMessage(), 2000),
                        ]);
                    }
                }
            }
        }

        if ($template->auto_whatsapp_enabled) {
            $whatsApp->dispatchForHistorico($historico, $disk, $path);
        }
    }
}
