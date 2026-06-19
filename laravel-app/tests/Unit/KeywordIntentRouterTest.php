<?php

namespace Tests\Unit;

use App\Services\AI\KeywordIntentRouter;
use Tests\TestCase;

class KeywordIntentRouterTest extends TestCase
{
    /** @test */
    public function it_resolves_training_intent_by_keywords(): void
    {
        $router = new KeywordIntentRouter;

        $this->assertSame('training', $router->resolve('Quero montar meu treino de pernas'));
    }

    /** @test */
    public function it_resolves_nutrition_intent_by_keywords(): void
    {
        $router = new KeywordIntentRouter;

        $this->assertSame('nutrition', $router->resolve('Qual dieta devo seguir hoje?'));
    }

    /** @test */
    public function it_returns_null_for_ambiguous_short_messages(): void
    {
        $router = new KeywordIntentRouter;

        $this->assertNull($router->resolve('ok'));
    }

    /** @test */
    public function it_resolves_support_intent(): void
    {
        $router = new KeywordIntentRouter;

        $this->assertSame('support', $router->resolve('Preciso de ajuda para usar o app'));
    }
}
