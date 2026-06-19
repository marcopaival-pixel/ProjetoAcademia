<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class RetentionAgent extends BaseAgent
{
    public function getName(): string
    {
        return 'retention';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        return [
            'ok' => true,
            'message' => "💪 **Estamos aqui para ajudar!**\n\n"
                ."Se está pensando em pausar ou cancelar, considere falar com seu profissional ou nosso suporte — podemos ajustar seu plano de treino ou nutrição.\n\n"
                ."Acesse **Suporte** para abrir um chamado. Sua jornada de saúde é importante para nós.\n\n"
                ."_Sua mensagem:_ {$message}",
            'tokens' => 0,
            'cost' => 0,
            'model' => 'rule_redirect',
        ];
    }
}
