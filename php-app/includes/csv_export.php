<?php
declare(strict_types=1);

/**
 * Envia cabeçalhos e corpo CSV UTF-8 (BOM) para download.
 *
 * @param iterable<int, list<string|int|float|null>> $rows Linhas já normalizadas como escalares.
 */
function send_csv_download(string $filename, array $header, iterable $rows): void
{
    $safe = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $filename) ?: 'export.csv';
    if (!str_ends_with(strtolower($safe), '.csv')) {
        $safe .= '.csv';
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $safe . '"');
    header('Cache-Control: no-store');

    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    if ($out === false) {
        return;
    }
    fputcsv($out, $header, ';');
    foreach ($rows as $row) {
        $line = [];
        foreach ($row as $cell) {
            if ($cell === null) {
                $line[] = '';
            } elseif (is_float($cell)) {
                $line[] = $cell;
            } else {
                $line[] = (string) $cell;
            }
        }
        fputcsv($out, $line, ';');
    }
    fclose($out);
}
