<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class RetentionAgent extends BaseAgent {
    public function getName(): string { return 'retention'; }
    public function execute(User $user, string $message, array $context = []): array {
        return ['ok' => true, 'message' => "Módulo de Retenção e Fidelização em desenvolvimento. Mensagem: " . $message, 'tokens' => 0];
    }
}
