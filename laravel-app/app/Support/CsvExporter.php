<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * @param  list<string>  $header
     * @param  iterable<int, list<string|int|float|null>>  $rows
     */
    public static function download(string $filename, array $header, iterable $rows): StreamedResponse
    {
        $safe = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $filename) ?: 'export.csv';
        if (! str_ends_with(strtolower($safe), '.csv')) {
            $safe .= '.csv';
        }

        return response()->streamDownload(function () use ($header, $rows) {
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
        }, $safe, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }
}
