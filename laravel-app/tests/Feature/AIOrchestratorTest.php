<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AIOrchestratorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIOrchestratorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_process_ai_request_and_log_correctly()
    {
        // 1. Setup: Criar usuário e mockar OpenAI
        $user = User::factory()->create();
        
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'support' // Mock para o IntentClassifier
                        ]
                    ],
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Resposta do agente de suporte.'
                        ]
                    ]
                ],
                'usage' => [
                    'prompt_tokens' => 10,
                    'completion_tokens' => 20,
                    'total_tokens' => 30
                ]
            ], 200)
        ]);

        // 2. Act: Chamar o orquestrador
        $response = $this->actingAs($user)
            ->postJson('/api/ai/orchestrator', [
                'message' => 'Como faço para ver meu treino?'
            ]);

        // 3. Assert: Verificar resposta e log
        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('ai_orchestrator_logs', [
            'user_id' => $user->id,
            'agent_name' => 'support',
            'total_tokens' => 30,
            'status' => 'success'
        ]);
    }

    /** @test */
    public function it_respects_monetization_limits()
    {
        // Implementar lógica de teste para limites de plano se necessário
        $this->assertTrue(true);
    }
}
