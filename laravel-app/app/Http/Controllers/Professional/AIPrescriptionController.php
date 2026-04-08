<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI;

class AIPrescriptionController extends Controller
{
    /**
     * Exibe o Assistente de Prescrição Dinâmica.
     */
    public function index()
    {
        return view('professional.ai-wizard');
    }

    /**
     * Gera uma sugestão de plano via IA.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:10',
            'type' => 'required|in:training,nutrition',
        ]);

        $apiKey = env('OPENAI_API_KEY');
        
        if (!$apiKey) {
            // Mock de resposta para demonstração se a chave não estiver no .env
            return response()->json([
                'success' => true,
                'is_mock' => true,
                'plan_name' => 'Plano Sugerido: ' . ucfirst($request->type),
                'content' => $this->getMockResponse($request->type, $request->prompt),
            ]);
        }

        try {
            $client = OpenAI::client($apiKey);
            
            $systemPrompt = $request->type === 'training' 
                ? "Você é um Personal Trainer especialista em biomecânica. Crie um plano de treino estruturado baseado no pedido do usuário. Retorne em formato JSON com as chaves: 'name' (string), 'description' (string), 'exercises' (array de objetos com 'name', 'sets', 'reps', 'notes')."
                : "Você é um Nutrólogo de alta performance. Crie uma estratégia nutricional baseada no pedido. Retorne em formato JSON com as chaves: 'name', 'strategy', 'meals' (array de objetos com 'time', 'foods', 'macros_est').";

            $response = $client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $request->prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            return response()->json([
                'success' => true,
                'data' => json_decode($response->choices[0]->message->content, true),
            ]);

        } catch (\Exception $e) {
            Log::error('Erro AI Prescription: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Falha ao conectar com o motor de IA.'], 500);
        }
    }

    private function getMockResponse($type, $userPrompt)
    {
        if ($type === 'training') {
            return [
                'name' => 'Treino Dinâmico: Hipertrofia Adaptativa',
                'description' => 'Plano gerado baseado na sua solicitação: ' . $userPrompt,
                'exercises' => [
                    ['name' => 'Supino Inclinado (Halteres)', 'sets' => '4', 'reps' => '8-10', 'notes' => 'Foco na cadência 3010'],
                    ['name' => 'Remada Curvada', 'sets' => '3', 'reps' => '12', 'notes' => 'Pico de contração de 2 segundos'],
                    ['name' => 'Agachamento Búlgaro', 'sets' => '3', 'reps' => '10 cada perna', 'notes' => 'Aumentar carga progressivamente'],
                ]
            ];
        }

        return [
            'name' => 'Estratégia Nutricional: Low Carb Revisitada',
            'strategy' => 'Foco em densidade nutricional e controle glicêmico.',
            'meals' => [
                ['time' => '07:00 - Desjejum', 'foods' => 'Omelete de 3 ovos com espinafre e queijo cottage', 'macros' => 'P: 25g, C: 4g, G: 18g'],
                ['time' => '12:30 - Almoço', 'foods' => 'Peixe grelhado, brócolis ao vapor e mix de castanhas', 'macros' => 'P: 35g, C: 12g, G: 22g'],
            ]
        ];
    }
}
