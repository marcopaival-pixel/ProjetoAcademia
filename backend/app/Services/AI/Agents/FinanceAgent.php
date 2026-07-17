<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class FinanceAgent extends BaseAgent {
    public function getName(): string { return 'finance'; }
    public function execute(User $user, string $message, array $context = []): array {
        return ['ok' => true, 'message' => "Módulo Financeiro em desenvolvimento. Mensagem: " . $message, 'tokens' => 0];
    }
}
