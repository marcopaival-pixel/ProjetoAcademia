<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthEndpointsTest extends TestCase
{
    public function test_web_health_endpoint_returns_ok(): void
    {
        $this->get(route('health.check'))
            ->assertOk()
            ->assertJsonStructure(['status']);
    }

    public function test_api_v1_health_endpoint_returns_ok(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonStructure(['status']);
    }

    public function test_framework_up_endpoint_returns_ok(): void
    {
        $this->get('/up')->assertOk();
    }
}
