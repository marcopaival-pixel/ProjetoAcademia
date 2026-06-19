<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Services\AI\OrchestratorService;
use App\Services\MonetizationService;
use App\Services\SecureFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaUploadController extends Controller
{
    use FormatsApiResponses;

    public function workoutPhoto(Request $request, OrchestratorService $orchestrator, MonetizationService $monetization, SecureFileService $secureFiles): JsonResponse
    {
        $user = $request->user();
        $request->validate(['photo' => ['required', 'image', 'max:10240']]);

        $access = $monetization->checkAccess($user, 'workout_import_photo');
        if (! $access['allowed']) {
            return $this->error($access['reason'] ?? 'Recurso não disponível no plano.', 403, 'plan_limit_reached');
        }

        $path = $secureFiles->storeSensitiveFile($request->file('photo'), 'workout_imports');
        $fullPath = storage_path('app/'.$path);

        $result = $orchestrator->run($user, 'Extraia os exercícios desta ficha de treino.', [
            'intent' => 'workout_sheet',
            'type' => 'photo_import',
            'clinicId' => $user->academy_company_id,
            'clinic_id' => $user->clinic_id,
            'feature_code' => 'workout_import_photo',
            'image_path' => $fullPath,
        ]);

        if (($result['status'] ?? '') !== 'success') {
            return $this->error($result['error'] ?? 'Falha ao processar a foto.', 500, 'vision_error');
        }

        return $this->success([
            'analysis' => $result['message'] ?? '',
            'action' => $result['action'] ?? null,
        ]);
    }

    public function nutritionPhoto(Request $request, OrchestratorService $orchestrator): JsonResponse
    {
        $user = $request->user();
        $request->validate(['photo' => ['required', 'image', 'max:5120']]);

        $storedPath = $request->file('photo')->store('nutrition_temp', 'public');
        $prompt = 'Analise esta foto de um prato de comida. Identifique os alimentos e estime os macros.';

        $result = $orchestrator->run($user, $prompt, [
            'intent' => 'meal_photo',
            'type' => 'photo_analysis',
            'clinicId' => $user->academy_company_id,
            'clinic_id' => $user->clinic_id,
            'feature_code' => 'analyze_body_photo',
            'image_path' => storage_path('app/public/'.$storedPath),
        ]);

        if (($result['status'] ?? '') !== 'success') {
            return $this->error('Falha ao analisar a foto.', 500, 'vision_error');
        }

        $json = preg_replace('/^.*?(\[.*\]).*?$/s', '$1', $result['message']);
        $foods = json_decode($json, true);

        if (! is_array($foods)) {
            return $this->error('Resposta da IA não pôde ser interpretada.', 422, 'parse_error');
        }

        return $this->success(['foods' => $foods]);
    }
}
