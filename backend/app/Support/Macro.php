<?php

namespace App\Support;

class Macro
{
    public static function barPercent(float $consumed, ?float $target): ?int
    {
        if ($target === null || $target <= 0) {
            return null;
        }

        return (int) min(100, max(0, round(100.0 * $consumed / $target)));
    }
}
