<?php

namespace App\Services\AI\Agents;

use App\Models\User;

abstract class BaseAgent
{
    /**
     * Executa a lógica do agente
     * 
     * @param User $user Usuário que fez a solicitação
     * @param string $message Mensagem do usuário
     * @param array $context Contexto adicional
     * @return array Resposta formatada da IA
     */
    abstract public function execute(User $user, string $message, array $context = []): array;

    /**
     * Retorna o nome identificador do agente
     */
    abstract public function getName(): string;
}
