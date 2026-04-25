<?php

namespace App\Support;

/**
 * Sugestões de host/porta/criptografia por provedor (o administrador pode ajustar).
 */
final class EmailProviderPreset
{
    /**
     * @return array{host: string, porta: int, criptografia: string, smtp_usuario_hint?: string}
     */
    public static function smtpDefaults(string $preset): ?array
    {
        return match ($preset) {
            'gmail' => ['host' => 'smtp.gmail.com', 'porta' => 587, 'criptografia' => 'tls'],
            'outlook' => ['host' => 'smtp.office365.com', 'porta' => 587, 'criptografia' => 'tls'],
            'hostgator' => ['host' => 'mail.seudominio.com', 'porta' => 587, 'criptografia' => 'tls'],
            'sendgrid' => ['host' => 'smtp.sendgrid.net', 'porta' => 587, 'criptografia' => 'tls', 'smtp_usuario_hint' => 'apikey'],
            'mailgun' => ['host' => 'smtp.mailgun.org', 'porta' => 587, 'criptografia' => 'tls'],
            'ses' => ['host' => 'email-smtp.us-east-1.amazonaws.com', 'porta' => 587, 'criptografia' => 'tls'],
            'custom' => null,
            default => null,
        };
    }
}
