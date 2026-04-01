<?php
declare(strict_types=1);

/**
 * SVG responsivo (viewBox) — série cronológica: mais antigo → mais recente.
 *
 * @param list<array{weighed_at: string, weight_kg: string|float}> $chronological
 */
function weight_chart_svg(array $chronological): string
{
    if (count($chronological) < 2) {
        return '';
    }

    $esc = static function (string $s): string {
        return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    };

    $weights = [];
    foreach ($chronological as $row) {
        $weights[] = (float) $row['weight_kg'];
    }
    $min = min($weights);
    $max = max($weights);
    if ($max - $min < 0.5) {
        $min -= 1.0;
        $max += 1.0;
    } else {
        $min -= 0.5;
        $max += 0.5;
    }
    if ($min < 1) {
        $min = 1.0;
    }

    $w = 640.0;
    $h = 220.0;
    $left = 44.0;
    $right = 16.0;
    $top = 20.0;
    $plotW = $w - $left - $right;
    $plotH = $h - $top - 40.0;
    $n = count($chronological);

    $points = [];
    $circles = [];
    foreach ($chronological as $i => $row) {
        $kg = (float) $row['weight_kg'];
        $x = $left + ($n === 1 ? $plotW / 2 : ($i * $plotW / ($n - 1)));
        $y = $top + (($max - $kg) / ($max - $min)) * $plotH;
        $points[] = sprintf('%.1f,%.1f', $x, $y);
        $circles[] = sprintf(
            '<circle cx="%.1f" cy="%.1f" r="3.5" fill="#1a2332" stroke="#3d9cf5" stroke-width="2"/>',
            $x,
            $y
        );
    }
    $poly = $esc(implode(' ', $points));

    $yLabelMin = sprintf('%.1f', $min);
    $yLabelMax = sprintf('%.1f', $max);
    $d0 = (string) $chronological[0]['weighed_at'];
    $d1 = (string) $chronological[$n - 1]['weighed_at'];
    try {
        $labStart = (new DateTimeImmutable($d0))->format('d/m');
        $labEnd = (new DateTimeImmutable($d1))->format('d/m');
    } catch (Exception $e) {
        $labStart = '';
        $labEnd = '';
    }

    $axisBottomY = $top + $plotH + 24.0;

    return '<svg class="weight-chart__svg" viewBox="0 0 '
        . (int) $w . ' ' . (int) $h . '" role="img" aria-label="Evolução do peso (kg) ao longo do tempo">'
        . '<rect x="' . sprintf('%.1f', $left) . '" y="' . sprintf('%.1f', $top)
        . '" width="' . sprintf('%.1f', $plotW) . '" height="' . sprintf('%.1f', $plotH)
        . '" fill="none" stroke="#2d3a4f" stroke-width="1" rx="4"/>'
        . '<text x="8" y="16" fill="#8b9cb3" font-size="11">' . $esc('kg') . '</text>'
        . '<text x="6" y="' . sprintf('%.1f', $top + 11) . '" fill="#8b9cb3" font-size="10">' . $esc($yLabelMax) . '</text>'
        . '<text x="6" y="' . sprintf('%.1f', $top + $plotH) . '" fill="#8b9cb3" font-size="10">' . $esc($yLabelMin) . '</text>'
        . '<polyline fill="none" stroke="#3d9cf5" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" points="'
        . $poly . '"/>'
        . implode('', $circles)
        . '<text x="' . sprintf('%.1f', $left) . '" y="' . sprintf('%.1f', $axisBottomY) . '" fill="#8b9cb3" font-size="10">'
        . $esc($labStart) . '</text>'
        . '<text x="' . sprintf('%.1f', $left + $plotW) . '" y="' . sprintf('%.1f', $axisBottomY)
        . '" fill="#8b9cb3" font-size="10" text-anchor="end">' . $esc($labEnd) . '</text>'
        . '</svg>';
}
