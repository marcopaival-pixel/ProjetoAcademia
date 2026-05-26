<?php

namespace Tests\Feature;

use App\Services\OmniChatService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class OmniWebhookTest extends TestCase
{
    public function test_rejects_when_secret_configured_and_header_missing(): void
    {
        Config::set('projeto.omni_webhook_secret', 'secret-token');

        $this->postJson(route('omni.webhook'), [
            'company_slug' => 'acme',
            'channel_type' => 'widget',
            'customer_id' => 'c1',
            'content' => 'hello',
        ])->assertStatus(401)
            ->assertJsonPath('status', 'error');
    }

    public function test_rejects_when_secret_wrong(): void
    {
        Config::set('projeto.omni_webhook_secret', 'secret-token');

        $this->postJson(route('omni.webhook'), [
            'company_slug' => 'acme',
            'channel_type' => 'widget',
            'customer_id' => 'c1',
            'content' => 'hello',
        ], ['X-Omni-Secret' => 'wrong'])
            ->assertStatus(401);
    }

    public function test_accepts_when_secret_matches(): void
    {
        Config::set('projeto.omni_webhook_secret', 'good-token');

        $msg = new \stdClass;
        $msg->id = 42;

        $this->mock(OmniChatService::class, function ($mock) use ($msg) {
            $mock->shouldReceive('handleIncomingMessage')
                ->once()
                ->andReturn($msg);
        });

        $this->postJson(route('omni.webhook'), [
            'company_slug' => 'acme',
            'channel_type' => 'widget',
            'customer_id' => 'c1',
            'content' => 'hello',
        ], ['X-Omni-Secret' => 'good-token'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message_id', 42);
    }

    public function test_omni_webhook_requires_secret_in_production(): void
    {
        $this->app['env'] = 'production';
        Config::set('projeto.omni_webhook_secret', '');

        $this->postJson(route('omni.webhook'), [
            'company_slug' => 'acme',
            'channel_type' => 'widget',
            'customer_id' => 'c1',
            'content' => 'hello',
        ])->assertStatus(503);
    }

    public function test_no_secret_allows_request_without_header_when_service_ok(): void
    {
        Config::set('app.env', 'local');
        Config::set('projeto.omni_webhook_secret', '');

        $msg = new \stdClass;
        $msg->id = 1;

        $this->mock(OmniChatService::class, function ($mock) use ($msg) {
            $mock->shouldReceive('handleIncomingMessage')
                ->once()
                ->andReturn($msg);
        });

        $this->postJson(route('omni.webhook'), [
            'company_slug' => 'acme',
            'channel_type' => 'widget',
            'customer_id' => 'c1',
            'content' => 'hello',
        ])->assertOk()
            ->assertJsonPath('message_id', 1);
    }
}
