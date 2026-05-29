<?php

namespace Tests\Feature;

use App\Models\MarketingBanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingTrackingThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_tracking_is_rate_limited_per_ip(): void
    {
        $banner = MarketingBanner::create([
            'title' => 'Test Banner',
            'is_active' => true,
            'display_type' => 'modal',
            'priority' => 1,
        ]);

        for ($i = 0; $i < 60; $i++) {
            $this->postJson(route('api.marketing.banners.view', $banner))->assertOk();
        }

        $this->postJson(route('api.marketing.banners.view', $banner))->assertStatus(429);
    }
}
