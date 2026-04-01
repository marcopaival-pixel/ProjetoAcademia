<?php

namespace Tests\Unit;

use App\Services\Nutrition;
use App\Support\Macro;
use PHPUnit\Framework\TestCase;

class NutritionMacroTest extends TestCase
{
    public function test_macro_bar_percent_respects_target(): void
    {
        $this->assertNull(Macro::barPercent(10.0, null));
        $this->assertSame(50, Macro::barPercent(50.0, 100.0));
        $this->assertSame(100, Macro::barPercent(200.0, 100.0));
    }

    public function test_default_macros_from_calories(): void
    {
        $m = Nutrition::defaultMacroTargetsFromKcal(2000);
        $this->assertGreaterThan(0, $m['p']);
        $this->assertGreaterThan(0, $m['c']);
        $this->assertGreaterThan(0, $m['f']);
    }
}
