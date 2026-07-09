<?php

namespace App\Mail;

use App\Models\FiscalInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FiscalInvoiceIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FiscalInvoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sua nota fiscal NexShape foi emitida'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fiscal-invoice-issued',
            with: ['invoice' => $this->invoice]
        );
    }
}
