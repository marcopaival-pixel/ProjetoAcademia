<?php

namespace App\Support;

final class EmailTemplateType
{
    public const CONFIRMACAO_CADASTRO = 'confirmacao_cadastro';

    public const RECUPERACAO_SENHA = 'recuperacao_senha';

    public const ENVIO_DOCUMENTO = 'envio_documento';

    public const NOTIFICACAO = 'notificacao';

    public const BOAS_VINDAS = 'boas_vindas';

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::CONFIRMACAO_CADASTRO => 'Confirmação de cadastro',
            self::RECUPERACAO_SENHA => 'Recuperação de senha',
            self::ENVIO_DOCUMENTO => 'Envio de documento',
            self::NOTIFICACAO => 'Notificação',
            self::BOAS_VINDAS => 'Boas-vindas',
        ];
    }
}
