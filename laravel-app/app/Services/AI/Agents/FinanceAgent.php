<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class FinanceAgent extends BaseAgent
{
    public function getName(): string
    {
        return 'finance';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        return [
            'ok' => true,
            'message' => "💳 **Financeiro NexShape**\n\n"
                ."Para consultar pagamentos, mensalidades e faturas, acesse **Meu Plano** ou **Financeiro** no menu.\n\n"
                ."Se precisar de ajuda com cobrança, abra um ticket em **Suporte** ou fale com o administrador da sua clínica.\n\n"
                ."_Sua mensagem:_ {$message}",
            'tokens' => 0,
            'cost' => 0,
            'model' => 'rule_redirect',
        ];
    }
}
