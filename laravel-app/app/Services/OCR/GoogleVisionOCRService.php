<?php

namespace App\Services\OCR;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

class GoogleVisionOCRService implements OCRServiceInterface
{
    /**
     * Extrai texto de uma imagem usando a API Google Cloud Vision.
     */
    public function extractText(string $imagePath): string
    {
        $apiKey = config('services.google_vision.key');
        
        if (!$apiKey) {
            // Se não houver chave, retorna erro ou um texto de simulação se estiver em dev
            if (app()->environment('local')) {
                return "Supino reto 4x10 40kg\nAgachamento livre 4x12 60kg\nRosca direta 3x12 15kg";
            }
            throw new Exception('Google Vision API Key not configured in services.google_vision.key.');
        }

        // Se o path for relativo ao storage
        $absolutePath = Storage::disk('public')->path($imagePath);
        if (!file_exists($absolutePath)) {
            throw new Exception("Imagem não encontrada no path: {$absolutePath}");
        }

        $imageData = base64_encode(file_get_contents($absolutePath));

        $response = Http::post("https://vision.googleapis.com/v1/images:annotate?key={$apiKey}", [
            'requests' => [
                [
                    'image' => [
                        'content' => $imageData,
                    ],
                    'features' => [
                        [
                            'type' => 'TEXT_DETECTION',
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->failed()) {
            throw new Exception('Falha na chamada da API Google Vision: ' . $response->body());
        }

        $result = $response->json();
        
        // Extrai o texto completo
        $text = $result['responses'][0]['fullTextAnnotation']['text'] ?? '';

        if (empty($text)) {
            // Tenta ver se há texto em blocos menores
            $textAnnotations = $result['responses'][0]['textAnnotations'] ?? [];
            $text = $textAnnotations[0]['description'] ?? '';
        }

        return $text;
    }
}
