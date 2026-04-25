<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PdfQrService
{
    public function validationUrl(string $codigoValidacao): string
    {
        $segment = trim(config('pdf.validation_path_segment', 'validar-documento'), '/');

        return rtrim(url('/'), '/').'/'.$segment.'/'.$codigoValidacao;
    }

    /**
     * PNG em data URI para incrustar no HTML do Dompdf.
     */
    public function pngDataUriForText(string $text, int $size = 140): string
    {
        $qr = QrCode::create($text)->setSize($size)->setMargin(4);
        $writer = new PngWriter();

        return $writer->write($qr)->getDataUri();
    }
}
