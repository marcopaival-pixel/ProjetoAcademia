<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntentClassifierService
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    /**
     * Classifica a intenção do usuário em uma das categorias suportadas
     */
    public function classify(string $message): string
    {
        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => "Você é um classificador de intenções para um sistema de gestão de academias e clínicas de saúde multiprofissional.
                    Dada a mensagem do usuário, retorne APENAS uma das seguintes palavras-chave:
                    - training: exercícios, treinos, séries, academia, musculação, cardio, performance.
                    - nutrition: dieta, suplementação, calorias, alimentos, água, refeição, plano alimentar.
                    - clinical: avaliações físicas, bioimpedância, insights de saúde, exames, composição corporal.
                    - pain: dor, EVA, fisioterapia, lesão, região dolorida, diário de dor.
                    - scheduling: consulta, agendamento, agenda, horário, remarcar, cancelar consulta.
                    - psychology: humor, bem-estar, emocional, ansiedade, sono, estresse, mindfulness, psicólogo.
                    - medic: receita médica, medicamento, prescrição, bula, dosagem, médico.
                    - shop: loja, produto, comprar, pedido, entrega, rastreio, carrinho, pontos, cashback, desconto.
                    - support: ajuda técnica, como usar o app, bugs, reclamações, senha, login.
                    - analytics: relatórios de progresso, gráficos de evolução, estatísticas.
                    - finance: pagamentos, mensalidades, faturas, planos.
                    - sales: compra de novos planos, upgrades, promoções.
                    - retention: cancelamentos, desmotivação, renovações.
                    
                    Se estiver em dúvida, retorne 'support'."
                ],
                ['role' => 'user', 'content' => $message]
            ];

            $response = $this->aiProvider->call(
                user: auth()->user(), // Fallback para o usuário logado
                messages: $messages,
                agentName: 'intent_classifier',
                modelType: 'fast',
                context: ['temperature' => 0, 'max_tokens' => 10]
            );

            if (!$response['ok']) {
                return 'support';
            }

            $intent = trim(strtolower($response['message'] ?? 'support'));
            
            // Sanitização básica
            $validIntents = [
                'training', 'nutrition', 'clinical', 'pain',
                'scheduling', 'psychology', 'medic', 'shop',
                'support', 'analytics', 'finance', 'sales', 'retention',
            ];
            
            return in_array($intent, $validIntents) ? $intent : 'support';

        } catch (Exception $e) {
            Log::error("Erro na classificação de intenção: " . $e->getMessage());
            return 'support';
        }
    }
}
