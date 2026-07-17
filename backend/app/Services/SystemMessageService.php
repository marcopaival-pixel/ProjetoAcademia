<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Message;

class SystemMessageService
{
    /**
     * Envia uma notificação para a central de mensagens do administrador sobre uma senha gerada.
     */
    public static function sendPasswordNotificationToAdmin(User $student, string $plainPassword, ?User $admin = null): void
    {
        // Se não for passado um administrador específico, busca o primeiro administrador do sistema
        if (!$admin) {
            $admin = User::where('is_admin', true)->first();
        }

        if (!$admin) {
            return;
        }

        // Encontrar ou criar conversa entre o aluno e o administrador
        $conversation = Conversation::where(function($q) use ($student, $admin) {
            $q->where('user_one_id', $student->id)->where('user_two_id', $admin->id);
        })->orWhere(function($q) use ($student, $admin) {
            $q->where('user_one_id', $admin->id)->where('user_two_id', $student->id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $student->id,
                'user_two_id' => $admin->id,
                'tipo' => Conversation::TIPO_SUPORTE,
                'status' => Conversation::STATUS_ABERTO,
            ]);
        }

        // Criar a mensagem de sistema
        // Usamos o ID do aluno como remetente para que a conversa apareça corretamente na lista do admin
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $student->id,
            'content' => "📢 [SISTEMA] Nova senha automática gerada.\n\n" .
                         "O utilizador **{$student->name}** (Aluno) recebeu uma nova senha de acesso.\n" .
                         "E-mail: `{$student->email}`\n" .
                         "Senha Temporária: `{$plainPassword}`\n\n" .
                         "Esta é uma mensagem automática de segurança.",
            'is_read' => false,
        ]);
    }
}
