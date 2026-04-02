<?php

namespace Tests\Feature;

use App\Models\AIChat;
use App\Models\User;
use App\Services\AIChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatQuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_user_blocked_when_daily_user_message_quota_reached(): void
    {
        config(['projeto.chat_free_daily_user_messages' => 2]);

        $user = User::factory()->create([
            'is_premium' => false,
            'premium_expires_at' => null,
        ]);

        for ($i = 0; $i < 2; $i++) {
            AIChat::create([
                'user_id' => $user->id,
                'role' => 'user',
                'message' => "mensagem {$i}",
            ]);
        }

        $this->actingAs($user)->postJson(route('chat.send'), [
            'message' => 'terceira',
        ])
            ->assertStatus(403)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('code', 'chat_quota_exceeded')
            ->assertJsonPath('quota.limit', 2)
            ->assertJsonPath('quota.used', 2);

        $this->assertSame(2, AIChat::query()->where('user_id', $user->id)->count());
    }

    public function test_history_includes_chat_quota_payload_for_free_user(): void
    {
        config(['projeto.chat_free_daily_user_messages' => 6]);

        $user = User::factory()->create(['is_premium' => false]);

        $this->actingAs($user)->getJson('/api/chat/history?limit=5')
            ->assertOk()
            ->assertJsonPath('chat_quota.is_premium', false)
            ->assertJsonPath('chat_quota.daily_user_limit', 6)
            ->assertJsonPath('chat_quota.daily_user_used', 0);
    }

    public function test_history_shows_premium_without_daily_limit(): void
    {
        $user = User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]);

        $this->actingAs($user)->getJson('/api/chat/history')
            ->assertOk()
            ->assertJsonPath('chat_quota.is_premium', true)
            ->assertJsonPath('chat_quota.daily_user_limit', null);
    }

    public function test_send_returns_chat_quota_after_success(): void
    {
        config(['projeto.chat_free_daily_user_messages' => 8]);

        $this->mock(AIChatService::class, function ($mock) {
            $mock->shouldReceive('chat')
                ->once()
                ->andReturn(['ok' => true, 'message' => 'Resposta de teste']);
        });

        $user = User::factory()->create(['is_premium' => false]);

        $this->actingAs($user)->postJson(route('chat.send'), ['message' => 'Olá'])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('chat_quota.is_premium', false)
            ->assertJsonPath('chat_quota.daily_user_limit', 8)
            ->assertJsonPath('chat_quota.daily_user_used', 1);
    }

    public function test_administrator_bypasses_daily_chat_quota(): void
    {
        config(['projeto.chat_free_daily_user_messages' => 1]);

        $this->mock(AIChatService::class, function ($mock) {
            $mock->shouldReceive('chat')
                ->once()
                ->andReturn(['ok' => true, 'message' => 'Ok']);
        });

        $user = User::factory()->administrator()->create([
            'is_premium' => false,
        ]);

        AIChat::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => 'já enviei uma hoje',
        ]);

        $this->actingAs($user)->postJson(route('chat.send'), ['message' => 'segunda mensagem'])
            ->assertOk()
            ->assertJsonPath('chat_quota.is_premium', true)
            ->assertJsonPath('chat_quota.daily_user_limit', null);
    }
}
