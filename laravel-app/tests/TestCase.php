<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        \App\Support\TenantContext::set(null);
        config(['services.openai.api_key' => 'test-key-phpunit']);
        $this->app->forgetInstance(\App\Services\AI\AIProviderService::class);
        $this->app->forgetInstance(\App\Services\AI\IntentClassifierService::class);
        $this->app->forgetInstance(\App\Services\AI\OrchestratorService::class);
    }

    /**
     * @return array<string, mixed>
     */
    protected function openAiChatFakePayload(string $content): array
    {
        return [
            'choices' => [
                ['message' => ['role' => 'assistant', 'content' => $content]],
            ],
            'usage' => [
                'prompt_tokens' => 10,
                'completion_tokens' => 20,
                'total_tokens' => 30,
            ],
        ];
    }
}
