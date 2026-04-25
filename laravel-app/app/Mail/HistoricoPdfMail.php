<?php

namespace App\Mail;

use App\Models\HistoricoPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class HistoricoPdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public HistoricoPdf $historico,
        public string $disk,
        public string $path
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Documento: '.($this->historico->numero_oficial ?? $this->historico->nome_arquivo),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.historico-pdf',
            with: [
                'historico' => $this->historico,
                'urlValidacao' => $this->historico->metadata['validation_url'] ?? '',
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $absolute = Storage::disk($this->disk)->path($this->path);

        return [
            Attachment::fromPath($absolute)
                ->as($this->historico->nome_arquivo)
                ->withMime('application/pdf'),
        ];
    }
}
