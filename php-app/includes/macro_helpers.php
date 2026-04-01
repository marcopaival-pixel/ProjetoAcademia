<?php
declare(strict_types=1);

/** Percentual 0–100 para barra; null se sem meta. */
function macro_bar_percent(float $consumed, ?float $target): ?int
{
    if ($target === null || $target <= 0) {
        return null;
    }
    return (int) min(100, max(0, round(100.0 * $consumed / $target)));
}
