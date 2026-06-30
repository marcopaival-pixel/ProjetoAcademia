<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LgpdDeletionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $reason,
        public string $adminUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[LGPD] Pedido de exclusão — utilizador #'.$this->user->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.lgpd-deletion-request',
        );
    }
}
