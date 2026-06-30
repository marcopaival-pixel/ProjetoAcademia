<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShopOrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ShopOrder $order,
        public string $event,
        public string $bodyText,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->event) {
            'paid' => 'Pedido confirmado — '.$this->order->order_number,
            'shipped' => 'Pedido enviado — '.$this->order->order_number,
            'cancelled' => 'Pedido cancelado — '.$this->order->order_number,
            default => 'Atualização do pedido — '.$this->order->order_number,
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(text: 'mail.shop-order-status');
    }
}
