<?php

namespace App\Services;

use App\Models\OmniConversation;
use App\Models\OmniMessage;
use App\Models\OmniChatbotRule;
use App\Models\OmniCompany;
use App\Models\OmniChannel;
use Exception;
use Illuminate\Support\Facades\Log;

class OmniChatService
{
    /**
     * Processa uma mensagem recebida de qualquer canal
     */
    public function handleIncomingMessage(array $data)
    {
        try {
            $company = OmniCompany::where('slug', $data['company_slug'])->firstOrFail();
            $channel = OmniChannel::where('company_id', $company->id)
                ->where('type', $data['channel_type'])
                ->firstOrFail();

            $conversation = $this->getOrCreateConversation($company, $channel, $data);

            // Salva a mensagem do cliente
            $message = OmniMessage::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'customer',
                'content' => $data['content'],
                'content_type' => $data['content_type'] ?? 'text',
            ]);

            $conversation->update(['last_message_at' => now()]);

            // Despacha o Job para processar a resposta de forma assíncrona
            \App\Jobs\OmniProcessMessage::dispatch($conversation, $data['content']);

            return $message;
        } catch (Exception $e) {
            Log::error("OmniChatService Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lógica de resposta executada pelo Job em background
     */
    public function replyToMessage(OmniConversation $conversation, string $userContent)
    {
        // Lógica de resposta (Bot ou Humano)
        if ($conversation->status === 'bot') {
            $this->processBotResponse($conversation, $userContent);
        }

        // Notifica atendentes via WebSocket (implementar depois com Reverb/Events)
        // event(new \App\Events\OmniMessageReceived($conversation->messages()->latest()->first()));
    }

    private function getOrCreateConversation($company, $channel, $data)
    {
        return OmniConversation::firstOrCreate(
            [
                'company_id' => $company->id,
                'channel_id' => $channel->id,
                'customer_external_id' => $data['customer_id'],
            ],
            [
                'customer_name' => $data['customer_name'] ?? 'Cliente',
                'status' => 'bot',
            ]
        );
    }

    private function processBotResponse($conversation, $userContent)
    {
        // 1. Verificar se existe um roteiro (Flow) ativo
        if ($conversation->current_bot_step_id) {
            $currentStep = \App\Models\OmniBotStep::with('options')->find($conversation->current_bot_step_id);
            
            if ($currentStep && $currentStep->type === 'menu') {
                $option = \App\Models\OmniBotOption::where('step_id', $currentStep->id)
                    ->where('trigger_value', trim($userContent))
                    ->first();

                if ($option) {
                    $this->moveToStep($conversation, $option->destination_step_id);
                    return;
                }
            }
        }

        // 2. Se não houver passo atual, inicia o fluxo ou usa Keywords
        $bot = \App\Models\OmniBot::where('company_id', $conversation->company_id)->where('is_active', true)->first();
        if ($bot && !$conversation->current_bot_step_id) {
            $startStep = $bot->steps()->where('is_start', true)->first();
            if ($startStep) {
                $this->moveToStep($conversation, $startStep->id);
                return;
            }
        }

        // 3. Fallback: Check Keywords (Lógica anterior)
        $rule = OmniChatbotRule::where('company_id', $conversation->company_id)
            ->where('is_active', true)
            ->where('trigger_type', 'keyword')
            ->where('pattern', 'LIKE', '%' . $userContent . '%')
            ->first();

        if ($rule) {
            $this->sendBotMessage($conversation, $rule->response);
            return;
        }

        // 4. Intent de transferência
        if ($this->isHumanRequest($userContent)) {
            $conversation->update(['status' => 'pending']);
            $this->sendBotMessage($conversation, "Um momento, estou transferindo você para um de nossos atendentes...");
            return;
        }

        // 5. Fallback via Orquestrador NexShape
        try {
            $orchestrator = app(\App\Services\AI\OrchestratorService::class);
            $user = \App\Models\User::where('academy_company_id', $conversation->company_id)->first(); // Fallback user
            
            $response = $orchestrator->run($user ?? auth()->user(), $userContent, [
                'intent' => 'support',
                'source' => 'omnichannel',
                'clinicId' => $conversation->company_id
            ]);
            
            if ($response['status'] === 'success') {
                $this->sendBotMessage($conversation, $response['message']);
                return;
            }
        } catch (\Exception $e) {
            Log::error("Omni AI Orchestrator Error: " . $e->getMessage());
        }

        // 4. Default Fallback
        $this->sendBotMessage($conversation, "Desculpe, não entendi. Gostaria de falar com um atendente humano?");
    }

    private function moveToStep($conversation, $stepId)
    {
        $step = \App\Models\OmniBotStep::with('options')->find($stepId);
        if (!$step) return;

        $conversation->update(['current_bot_step_id' => $step->id]);
        
        $content = $step->content;
        
        // Se for menu, anexa as opções no texto
        if ($step->type === 'menu' && $step->options->count() > 0) {
            $content .= "\n\n";
            foreach($step->options as $opt) {
                $content .= "{$opt->trigger_value}. {$opt->label}\n";
            }
        }

        $this->sendBotMessage($conversation, $content);

        // Se o passo for de transferência, libera para o humano
        if ($step->type === 'transfer') {
            $conversation->update(['status' => 'pending']);
        }

        // Se tiver um próximo passo linear e não for menu/question, agendar/mover (simplificado)
        if ($step->type === 'message' && $step->next_step_id) {
            $this->moveToStep($conversation, $step->next_step_id);
        }
    }

    private function sendBotMessage($conversation, $content)
    {
        OmniMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'bot',
            'content' => $content,
            'content_type' => 'text',
        ]);
    }

    private function isHumanRequest($text): bool
    {
        $keywords = ['atendente', 'humano', 'falar com alguém', 'pessoa', 'suporte', 'vendas'];
        foreach ($keywords as $word) {
            if (stripos($text, $word) !== false) return true;
        }
        return false;
    }
}
