<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoRoutesProductionTest extends TestCase
{
    private function useProductionEnvironment(): void
    {
        $this->app['env'] = 'production';
    }

    public function test_demo_start_is_not_available_in_production(): void
    {
        $this->useProductionEnvironment();

        $this->get(route('demo.start'))->assertStatus(404);
    }

    public function test_demo_switch_is_not_available_in_production(): void
    {
        $this->useProductionEnvironment();

        $this->post(route('demo.switch'), ['profile' => 'admin'])->assertStatus(404);
    }
}
