<?php

namespace App\Services\Shop;

use App\Mail\ShopOrderStatusMail;
use App\Models\ShopOrder;
use App\Services\MessagingService;
use App\Services\TransactionalMailService;
use App\Support\MailSendType;
use Illuminate\Support\Facades\Log;

class ShopOrderNotificationService
{
    public function __construct(private TransactionalMailService $mailService) {}

    public function notifyOrderPaid(ShopOrder $order): void
    {
        $order->loadMissing('user');
        $user = $order->user;
        if ($user === null) {
            return;
        }

        $total = number_format((float) $order->total, 2, ',', '.');
        $body = "Seu pedido foi confirmado com pagamento de R$ {$total}. Acompanhe o status em Meus Pedidos.";

        $this->sendInternal($user->id, 'Pedido pago — '.$order->order_number, $body, $order, 'paid');
        $this->sendEmail($order, 'paid', $body, $user);
    }

    public function notifyOrderShipped(ShopOrder $order): void
    {
        $order->loadMissing('user');
        $user = $order->user;
        if ($user === null) {
            return;
        }

        $tracking = $order->tracking_code
            ? " Código de rastreamento: {$order->tracking_code}."
            : '';
        $body = "Seu pedido foi despachado.{$tracking}";

        $this->sendInternal($user->id, 'Pedido enviado — '.$order->order_number, $body, $order, 'shipped');
        $this->sendEmail($order, 'shipped', $body, $user);
    }

    public function notifyOrderCancelled(ShopOrder $order, string $reason = ''): void
    {
        $order->loadMissing('user');
        $user = $order->user;
        if ($user === null) {
            return;
        }

        $suffix = $reason !== '' ? " Motivo: {$reason}." : '';
        $body = "Seu pedido foi cancelado ou reembolsado.{$suffix}";

        $this->sendInternal($user->id, 'Pedido cancelado — '.$order->order_number, $body, $order, 'cancelled');
        $this->sendEmail($order, 'cancelled', $body, $user);
    }

    private function sendInternal(int $userId, string $subject, string $body, ShopOrder $order, string $event): void
    {
        try {
            MessagingService::sendSystemMessage($userId, $subject, $body);
        } catch (\Throwable $e) {
            Log::warning('ShopOrderNotification: falha na mensagem interna.', [
                'order_id' => $order->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendEmail(ShopOrder $order, string $event, string $body, $user): void
    {
        try {
            $this->mailService->sendToUser(
                new ShopOrderStatusMail($order, $event, $body),
                $user,
                $user->academy_company_id,
                MailSendType::NOTIFICATION,
                'Shopping — '.$order->order_number,
                $body,
                [],
                [],
                false
            );
        } catch (\Throwable $e) {
            Log::warning('ShopOrderNotification: falha no e-mail transacional.', [
                'order_id' => $order->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
