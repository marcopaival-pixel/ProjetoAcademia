<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class DompdfPdfService
{
    /**
     * @return non-empty-string Binary PDF content
     */
    public function render(
        string $html,
        string $paper = 'A4',
        string $orientation = 'portrait',
        bool $remoteEnabled = false,
        string $defaultFont = 'DejaVu Sans'
    ): string {
        if (! class_exists(Dompdf::class)) {
            throw new \RuntimeException(
                'Dompdf não está disponível. Execute composer install na pasta laravel-app.'
            );
        }

        $options = new Options;
        $options->set('isRemoteEnabled', $remoteEnabled);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', $defaultFont);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        $output = $dompdf->output();
        if (! is_string($output) || $output === '') {
            throw new \RuntimeException('Falha ao gerar o PDF.');
        }

        return $output;
    }
}
