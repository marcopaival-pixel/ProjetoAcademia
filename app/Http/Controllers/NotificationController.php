<?php

namespace App\Http\Controllers;

use App\Models\InternalEmail;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Obter todas as contagens de itens não lidos em uma única requisição.
     */
    public function unreadCounts(): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['emails' => 0, 'messages' => 0], 401);
        }

        // 1. Contagem de e-mails internos (Inbox) - DESATIVADO
        $emails = 0;

        // 2. Contagem de mensagens diretas (Chat)
        $messages = Message::whereHas('conversation', function($q) use ($userId) {
                $q->where('user_one_id', $userId)->orWhere('user_two_id', $userId);
            })
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->whereHas('sender', function($q) use ($userId) {
                // Não contar mensagens de quem eu bloqueei
                $q->whereDoesntHave('blockers', function($sq) use ($userId) { 
                    $sq->where('blocker_id', $userId); 
                });
            })
            ->count();

        return response()->json([
            'emails' => $emails,
            'messages' => $messages,
            'total' => $emails + $messages
        ]);
    }
}
