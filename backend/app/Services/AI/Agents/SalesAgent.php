<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class SalesAgent extends BaseAgent {
    public function getName(): string { return 'sales'; }
    public function execute(User $user, string $message, array $context = []): array {
        return ['ok' => true, 'message' => "Módulo de Vendas e Upgrades em desenvolvimento. Mensagem: " . $message, 'tokens' => 0];
    }
}
