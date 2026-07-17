<?php

namespace App\Services;

use App\Models\InternalEmail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MessagingService
{
    /**
     * Send a system message to a user.
     *
     * @param int $recipientId
     * @param string $subject
     * @param string $content
     * @return InternalEmail
     */
    public static function sendSystemMessage(int $recipientId, string $subject, string $content): InternalEmail
    {
        $systemSenderId = User::query()->where('is_admin', true)->orderBy('id')->value('id') ?? 1;

        $message = InternalEmail::create([
            'sender_id' => $systemSenderId,
            'recipient_id' => $recipientId,
            'subject' => $subject,
            'content' => $content,
            'sent_at' => now(),
            'status' => 'sent',
            'is_system' => true,
        ]);

        Log::info("System message sent to User " . $recipientId, ['msg_id' => $message->id]);

        return $message;
    }

    /**
     * Notify about a new Kanban task (Integration placeholder).
     */
    public static function notifyNewTask(int $userId, string $taskTitle)
    {
        return self::sendSystemMessage(
            $userId, 
            "Nova Tarefa Kanban: " . $taskTitle,
            "Uma nova tarefa foi atribuída a você no quadro Kanban: " . $taskTitle . ". Por favor, verifique seus próximos passos."
        );
    }
}
