<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class SalesAgent extends BaseAgent
{
    public function getName(): string
    {
        return 'sales';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        return [
            'ok' => true,
            'message' => "🚀 **Planos e Upgrades**\n\n"
                ."Confira os planos disponíveis em **Assinatura / Meu Plano** para desbloquear recursos premium.\n\n"
                ."Promoções ativas aparecem no dashboard principal. Para propostas comerciais, contacte o representante da sua clínica.\n\n"
                ."_Sua mensagem:_ {$message}",
            'tokens' => 0,
            'cost' => 0,
            'model' => 'rule_redirect',
        ];
    }
}
