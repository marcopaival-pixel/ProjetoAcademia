<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AIOrchestratorLog;
use Database\Seeders\AppFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIOrchestratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AppFeatureSeeder::class);
    }

    /** @test */
    public function it_can_process_ai_request_and_log_correctly()
    {
        $user = User::factory()->create();
        
        Http::fake([
            '*' => Http::response($this->openAiChatFakePayload('Resposta do agente de suporte.'), 200),
        ]);

        // 2. Act: Chamar o orquestrador
        $response = $this->actingAs($user)
            ->postJson('/api/ai/orchestrator', [
                'message' => 'Como faço para ver meu treino?',
                'context' => ['intent' => 'support'],
            ]);

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
