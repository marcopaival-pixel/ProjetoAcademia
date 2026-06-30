Olá{{ $order->user?->name ? ', '.$order->user->name : '' }},

{{ $bodyText }}

Pedido: {{ $order->order_number }}
Total: R$ {{ number_format((float) $order->total, 2, ',', '.') }}

Acompanhe em: {{ route('shopping.orders.show', $order) }}

— Shopping Fitness
