<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowAiCreditsNotification extends Notification
{
    use Queueable;

    protected $balance;
    protected $isExhausted;

    /**
     * Create a new notification instance.
     */
    public function __construct($balance, $isExhausted = false)
    {
        $this->balance = $balance;
        $this->isExhausted = $isExhausted;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->isExhausted) {
            return (new MailMessage)
                ->subject('Seus créditos de IA acabaram')
                ->line('Seus créditos de IA acabaram.')
                ->action('Comprar Créditos', route('ai-credits.index'))
                ->line('Compre novos créditos para continuar utilizando os recursos de IA.');
        }
 
        return (new MailMessage)
            ->subject('Seus créditos de IA estão acabando')
            ->line('Seus créditos de IA estão baixos (Saldo: ' . $this->balance . ').')
            ->action('Adquirir Créditos', route('ai-credits.index'))
            ->line('Adquira mais créditos para continuar utilizando os recursos sem interrupções.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'balance' => $this->balance,
            'is_exhausted' => $this->isExhausted,
            'message' => $this->isExhausted 
                ? 'Seus créditos de IA acabaram. Compre novos créditos para continuar.'
                : 'Seus créditos de IA estão acabando. Adquira mais créditos para continuar utilizando os recursos.',
        ];
    }
}
