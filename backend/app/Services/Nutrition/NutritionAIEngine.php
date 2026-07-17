<?php

namespace App\Services\Nutrition;

use App\Models\User;
use App\Services\AI\OrchestratorService;
use App\Services\AiCreditService;
use Illuminate\Http\UploadedFile;

class NutritionAIEngine
{
    public function __construct(
        private OrchestratorService $orchestrator,
        private AiCreditService $credits,
    ) {}

    public function analyzeText(User $user, string $text): array
    {
        $text = trim($text);
        if ($text === '') {
            return $this->error('invalid_input', 'Descreva a refeicao antes de usar a IA.');
        }

        if (! $this->credits->hasCredits($user, 'nutrition_text_analysis')) {
            return $this->error('credits_exceeded', 'Creditos de IA insuficientes para analisar a refeicao.');
        }

        $prompt = implode("\n", [
            'Voce esta em um fluxo interno. Nao converse com o usuario.',
            'Extraia alimentos, quantidades e macros da refeicao abaixo.',
            'Retorne APENAS uma lista JSON no formato:',
            '[{"name":"Frango grelhado","amount":"200g","kcal":330,"p":62,"c":0,"f":7}]',
            "Refeicao: {$text}",
        ]);

        $result = $this->orchestrator->run($user, $prompt, [
            'intent' => 'nutrition',
            'type' => 'nutrition_text_analysis',
            'input_type' => 'text',
            'clinicId' => $user->academy_company_id,
        ]);

        $normalized = $this->normalizeOrchestratorResult($result, 'text');
        if ($normalized['success']) {
            $this->credits->consume($user, 'nutrition_text_analysis', [
                'input_type' => 'text',
                'preview' => mb_substr($text, 0, 120),
            ]);
        }

        return $normalized;
    }

    public function analyzePhoto(User $user, UploadedFile $photo): array
    {
        $validation = $this->validateFoodPhotoCandidate($photo->getRealPath(), (int) $photo->getSize());
        if (! $validation['ok']) {
            return $this->error('photo_validation_failed', $validation['message']);
        }

        if (! $this->credits->hasCredits($user, 'nutrition_photo_analysis')) {
            return $this->error('credits_exceeded', 'Creditos de IA insuficientes para analisar a foto da refeicao.');
        }

        $storedPath = $photo->store('nutrition_temp', 'public');
        $absolutePath = storage_path('app/public/' . $storedPath);

        $prompt = implode("\n", [
            'Voce esta em um fluxo interno. Nao converse com o usuario.',
            'Primeiro confirme se a imagem mostra comida/refeicao.',
            'Se nao for comida, retorne document_type unknown_document.',
            'Se for refeicao, identifique alimentos, estime quantidades e macros.',
            'No agente final, retorne APENAS uma lista JSON no formato:',
            '[{"name":"Frango grelhado","amount":"150g","kcal":250,"p":35,"c":0,"f":8}]',
        ]);

        $result = $this->orchestrator->run($user, $prompt, [
            'intent' => 'nutrition',
            'type' => 'nutrition_photo_analysis',
            'input_type' => 'photo',
            'clinicId' => $user->academy_company_id,
            'image_path' => $absolutePath,
            'photo_url' => asset('storage/' . $storedPath),
        ]);

        $normalized = $this->normalizeOrchestratorResult($result, 'photo');
        if ($normalized['success']) {
            $this->credits->consume($user, 'nutrition_photo_analysis', [
                'input_type' => 'photo',
                'size' => $photo->getSize(),
            ]);
        }

        return $normalized;
    }

    private function normalizeOrchestratorResult(array $result, string $source): array
    {
        if (($result['status'] ?? null) !== 'success') {
            return $this->error(
                $result['status'] ?? 'ai_error',
                $result['error'] ?? $result['message'] ?? 'Nao foi possivel analisar a refeicao.',
            );
        }

        if (($result['intent'] ?? null) === 'unknown_document') {
            return $this->error('not_food', 'A imagem nao foi reconhecida como uma refeicao.');
        }

        $foods = $this->extractFoods((string) ($result['message'] ?? ''));
        if ($foods === []) {
            return $this->error('invalid_ai_output', 'A IA nao retornou alimentos em formato estruturado.');
        }

        $confidence = $this->confidenceFromResult($result);

        return [
            'success' => true,
            'status' => $confidence >= 0.9 ? 'ready_to_save' : 'needs_review',
            'source' => $source,
            'confidence' => $confidence,
            'requires_review' => $confidence < 0.9,
            'foods' => $foods,
            'warnings' => $confidence < 0.9 ? ['Confira os alimentos antes de registrar.'] : [],
            'message' => 'Refeicao analisada com sucesso.',
        ];
    }

    private function extractFoods(string $message): array
    {
        $json = preg_replace('/^.*?(\[.*\]).*?$/s', '$1', $message);
        $decoded = json_decode((string) $json, true);
        if (! is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->filter(fn ($item) => is_array($item))
            ->map(fn ($item) => [
                'name' => (string) ($item['name'] ?? $item['food_name'] ?? 'Alimento identificado'),
                'amount' => (string) ($item['amount'] ?? '1 porcao'),
                'kcal' => max(0, (int) round((float) ($item['kcal'] ?? $item['calories'] ?? 0))),
                'p' => max(0, round((float) ($item['p'] ?? $item['protein_g'] ?? 0), 1)),
                'c' => max(0, round((float) ($item['c'] ?? $item['carbs_g'] ?? 0), 1)),
                'f' => max(0, round((float) ($item['f'] ?? $item['fat_g'] ?? 0), 1)),
            ])
            ->values()
            ->all();
    }

    private function confidenceFromResult(array $result): float
    {
        $vision = $result['structured_data'] ?? [];
        $confidence = $vision['confidence'] ?? $result['confidence'] ?? 0.86;

        return max(0, min(1, (float) $confidence));
    }

    private function validateFoodPhotoCandidate(string $path, int $size): array
    {
        $info = @getimagesize($path);
        if (! $info) {
            return ['ok' => false, 'message' => 'Arquivo de imagem invalido.'];
        }

        [$width, $height] = $info;
        if ($width < 240 || $height < 240) {
            return ['ok' => false, 'message' => 'A foto esta pequena demais para analise de refeicao.'];
        }

        if ($size < 20_000) {
            return ['ok' => false, 'message' => 'A imagem parece muito leve/sem detalhe para identificar alimentos.'];
        }

        return ['ok' => true, 'message' => 'Imagem candidata aprovada para analise de refeicao.'];
    }

    private function error(string $code, string $message): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'code' => $code,
            'error' => $message,
            'foods' => [],
            'warnings' => [$message],
        ];
    }
}
