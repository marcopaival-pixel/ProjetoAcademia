<?php

namespace App\Mail\Dunning;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SubscriptionSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sua assinatura foi suspensa - NexShape',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2>Olá, {$this->user->name}</h2>
                <p>Infelizmente, como não conseguimos processar o pagamento da sua assinatura nos últimos dias, o acesso aos recursos premium da sua conta NexShape foi <strong>suspenso</strong>.</p>
                <p>Você pode reativar sua assinatura a qualquer momento acessando o painel e atualizando o método de pagamento.</p>
                <br>
                <p>Atenciosamente,<br>Equipe NexShape</p>
            </div>
            "
        );
    }
}
