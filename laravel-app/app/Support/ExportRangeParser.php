<?php

namespace App\Support;

class ExportRangeParser
{
    /**
     * @return array{ok: true, from: ?string, to: ?string}|array{ok: false, message: string}
     */
    public static function parse(?string $fromRaw, ?string $toRaw): array
    {
        $fromRaw = trim((string) $fromRaw);
        $toRaw = trim((string) $toRaw);
        $from = $fromRaw !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromRaw) ? $fromRaw : null;
        $to = $toRaw !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $toRaw) ? $toRaw : null;
        if ($from !== null && $to !== null && $from > $to) {
            return ['ok' => false, 'message' => 'Data inicial não pode ser maior que a final.'];
        }

        return ['ok' => true, 'from' => $from, 'to' => $to];
    }

    /**
     * @param  array{from: ?string, to: ?string}  $range
     * @return array{clause: string, params: list<string>}
     */
    public static function sqlDateRangeClause(string $column, array $range): array
    {
        $f = $range['from'];
        $t = $range['to'];
        if ($f !== null && $t !== null) {
            return ['clause' => " AND {$column} BETWEEN ? AND ?", 'params' => [$f, $t]];
        }
        if ($f !== null) {
            return ['clause' => " AND {$column} >= ?", 'params' => [$f]];
        }
        if ($t !== null) {
            return ['clause' => " AND {$column} <= ?", 'params' => [$t]];
        }

        return ['clause' => '', 'params' => []];
    }
}
