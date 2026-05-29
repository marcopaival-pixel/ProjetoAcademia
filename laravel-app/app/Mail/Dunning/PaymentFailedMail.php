<?php

namespace App\Mail\Dunning;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $daysOverdue;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, int $daysOverdue)
    {
        $this->user = $user;
        $this->daysOverdue = $daysOverdue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aviso: Falha no Pagamento da sua Assinatura - NexShape',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Usar uma view genérica por enquanto. Em um cenário real, 
        // criaríamos a view resources/views/emails/dunning/payment-failed.blade.php
        return new Content(
            htmlString: "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2>Olá, {$this->user->name}</h2>
                <p>Notamos que houve uma falha no pagamento da sua assinatura na plataforma NexShape.</p>
                <p>O seu pagamento está com <strong>{$this->daysOverdue} dia(s) de atraso</strong>.</p>
                <p>Para evitar a suspensão dos seus acessos, por favor, atualize sua forma de pagamento o quanto antes.</p>
                <br>
                <p>Atenciosamente,<br>Equipe NexShape</p>
            </div>
            "
        );
    }
}
