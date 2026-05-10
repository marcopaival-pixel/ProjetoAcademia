<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForcedPasswordResetAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tempPassword;
    public $admin;
    public $dateTime;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $tempPassword, User $admin)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
        $this->admin = $admin;
        $this->dateTime = now()->format('d/m/Y H:i:s');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset forçado de senha realizado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.forced_reset_admin_notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
