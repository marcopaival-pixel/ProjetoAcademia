<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Obter ou criar a conversa entre o paciente atual e o profissional ativo no contexto.
     */
    private function getConversation()
    {
        $patientUserId = auth()->id();
        $context = request()->attributes->get('active_patient_link')
            ?? request()->attributes->get('active_patient_context');
        if (! $context) {
            abort(400, 'Contexto de vínculo não informado.');
        }
        $professionalUserId = $context->professional_id;

        // Busca uma conversa existente entre os dois
        $conversation = Conversation::where(function ($q) use ($patientUserId, $professionalUserId) {
            $q->where('user_one_id', $patientUserId)
              ->where('user_two_id', $professionalUserId);
        })->orWhere(function ($q) use ($patientUserId, $professionalUserId) {
            $q->where('user_one_id', $professionalUserId)
              ->where('user_two_id', $patientUserId);
        })->first();

        // Se não existir, cria uma
        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $patientUserId,
                'user_two_id' => $professionalUserId,
                'tipo' => Conversation::TIPO_SUPORTE,
                'status' => Conversation::STATUS_ABERTO,
            ]);
        }

        return $conversation;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $conversation = $this->getConversation();
        $patientUserId = auth()->id();

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($patientUserId) {
                return [
                    'id' => $msg->id,
                    'content' => $msg->content,
                    'is_mine' => (int) $msg->sender_id === (int) $patientUserId,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'data' => $messages,
            'conversation_id' => $conversation->id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $conversation = $this->getConversation();
        $patientUserId = auth()->id();

        $message = $conversation->messages()->create([
            'sender_id' => $patientUserId,
            'content' => $request->content,
            'is_read' => false,
        ]);

        // Dispara o evento de WebSockets
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Mensagem enviada com sucesso.',
            'data' => [
                'id' => $message->id,
                'content' => $message->content,
                'is_mine' => true,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at->toIso8601String(),
            ]
        ]);
    }
}
