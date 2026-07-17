<?php

namespace App\Support;

/**
 * Tipos de envio transacional para auditoria (log_envio_email.tipo_envio).
 */
final class MailSendType
{
    public const EMAIL_VERIFICATION = 'confirmacao_email';

    public const WELCOME = 'boas_vindas';

    public const PASSWORD_RESET = 'recuperacao_senha';

    public const PDF = 'pdf';

    public const NOTIFICATION = 'notificacao';

    public const REPORT = 'relatorio';

    public const SYSTEM_ALERT = 'aviso_sistema';

    public const TEST = 'teste';

    public const OTHER = 'outro';
}
