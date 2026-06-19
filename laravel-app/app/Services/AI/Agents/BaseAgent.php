<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

abstract class BaseAgent
{
    abstract public function execute(User $user, string $message, array $context = []): array;

    abstract public function getName(): string;

    /**
     * Utilizador cujos dados clínicos/treino devem ser consultados (paciente ou o próprio user).
     */
    protected function resolveSubjectUser(User $actor, array $context): User
    {
        $patientId = $context['patient_id'] ?? null;

        if ($patientId && (int) $patientId !== (int) $actor->id) {
            if ($actor->isAdministrator()) {
                return User::findOrFail((int) $patientId);
            }

            if ($actor->isProfessional() || $actor->hasRole(['instructor', 'supervisor'])) {
                if (! $actor->patients()->where('users.id', (int) $patientId)->exists()) {
                    throw new AuthorizationException(
                        'Acesso não autorizado aos dados deste paciente para IA.'
                    );
                }

                return User::findOrFail((int) $patientId);
            }
        }

        return $actor;
    }

    /**
     * Injeta histórico conversacional antes da última mensagem do usuário.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    protected function injectChatHistory(array &$messages, array $context): void
    {
        if (empty($context['chat_history']) || ! is_array($context['chat_history'])) {
            return;
        }

        $history = array_slice($context['chat_history'], -config('ai.chat_history_messages', 6));
        $userIndex = null;

        for ($i = count($messages) - 1; $i >= 0; $i--) {
            if (($messages[$i]['role'] ?? '') === 'user') {
                $userIndex = $i;
                break;
            }
        }

        if ($userIndex === null) {
            return;
        }

        array_splice($messages, $userIndex, 0, $history);
    }
}
