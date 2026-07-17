<?php

namespace App\Services\OCR;

interface OCRServiceInterface
{
    /**
     * Extrai texto de uma imagem.
     * 
     * @param string $imagePath Caminho absoluto ou relativo da imagem no storage.
     * @return string O texto extraído.
     */
    public function extractText(string $imagePath): string;
}
