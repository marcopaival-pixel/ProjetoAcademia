<?php

namespace App\Support;

/**
 * Gateways com driver implementado no código (PaymentGatewayManager).
 * Outros nomes não devem aparecer como configuráveis no admin.
 */
final class PaymentGatewayRegistry
{
    public const IMPLEMENTED = ['mercadopago', 'asaas'];

    /**
     * @return array<string, string> slug => rótulo UI
     */
    public static function options(): array
    {
        return [
            'mercadopago' => 'Mercado Pago',
            'asaas' => 'Asaas',
        ];
    }

    public static function isImplemented(string $gateway): bool
    {
        return in_array($gateway, self::IMPLEMENTED, true);
    }

    public static function validationRule(): string
    {
        return 'in:'.implode(',', self::IMPLEMENTED);
    }
}
