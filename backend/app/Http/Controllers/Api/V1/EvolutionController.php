<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EvolutionPhoto;
use App\Models\EvolutionReport;
use App\Models\EvolutionSessionAnalysis;
use App\Services\AI\Agents\VisionAgent;
use App\Services\AI\EvolutionReportOrchestratorService;
use App\Services\BodyPhotoValidationService;
use App\Services\EvolutionReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvolutionController extends Controller
{
    public function photos(Request $request): JsonResponse
    {
        $photos = EvolutionPhoto::where('user_id', $request->user()->id)
            ->latest('registered_date')
            ->latest()
            ->get()
            ->map(fn (EvolutionPhoto $photo) => $this->photoPayload($photo));

        return response()->json(['data' => ['photos' => $photos]]);
    }

    public function validatePhoto(Request $request, BodyPhotoValidationService $validator): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        return response()->json(['data' => $validator->validate($request->user(), $request->file('photo'))]);
    }

    public function uploadPhoto(Request $request, BodyPhotoValidationService $validator): JsonResponse
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'type' => 'required|in:front,side,right_side,left_side,back,custom',
            'registered_date' => 'required|date',
            'weight_kg' => 'nullable|numeric',
        ]);

        $validation = $validator->validate($request->user(), $request->file('photo'));
        if (!($validation['approved'] ?? false)) {
            return response()->json([
                'error' => [
                    'message' => implode(' ', $validation['messages'] ?? ['A foto enviada não foi aprovada.']),
                    'validation' => $validation,
                ],
            ], 422);
        }

        $path = $request->file('photo')->store('evolution', 'public');
        $photo = EvolutionPhoto::create([
            'user_id' => $request->user()->id,
            'photo_path' => $path,
            'type' => $validated['type'],
            'registered_date' => $validated['registered_date'],
            'weight_kg' => $validated['weight_kg'] ?? null,
            'notes' => json_encode([
                'source' => 'android_evolution_upload',
                'validation' => $validation,
            ], JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json(['data' => $this->photoPayload($photo)], 201);
    }

    public function deletePhoto(Request $request, EvolutionPhoto $photo): JsonResponse
    {
        abort_unless((int) $photo->user_id === (int) $request->user()->id, 404);

        $sessionDate = $photo->registered_date;
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        EvolutionSessionAnalysis::where('user_id', $request->user()->id)
            ->whereDate('session_date', $sessionDate)
            ->delete();

        return response()->json(['data' => ['deleted' => true]]);
    }

    public function analyzeSession(Request $request, VisionAgent $visionAgent): JsonResponse
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return response()->json(['error' => ['message' => 'Funcionalidade exclusiva para membros Premium.']], 403);
        }
        $aiCredits = app(\App\Services\AiCreditService::class);

        $validated = $request->validate(['date' => 'required|date']);
        $photos = EvolutionPhoto::where('user_id', $user->id)
            ->whereDate('registered_date', $validated['date'])
            ->whereIn('type', ['front', 'back', 'right_side', 'left_side', 'side'])
            ->get();

        if ($photos->count() < 2) {
            return response()->json(['error' => ['message' => 'A sessão precisa de pelo menos dois ângulos para análise.']], 422);
        }

        $photoHash = hash('sha256', $photos->sortBy('id')->map(fn ($photo) => "{$photo->id}:{$photo->photo_path}:{$photo->updated_at?->timestamp}")->implode('|'));
        $cached = EvolutionSessionAnalysis::where('user_id', $user->id)
            ->whereDate('session_date', $validated['date'])
            ->where('photo_hash', $photoHash)
            ->latest()
            ->first();

        if ($cached) {
            return response()->json(['data' => ['analysis' => $cached->analysis, 'cached' => true]]);
        }

        if (! $aiCredits->hasCredits($user, 'evolution_session_analysis')) {
            return response()->json([
                'error' => [
                    'code' => 'credits_exceeded',
                    'message' => 'Creditos de IA insuficientes para analisar esta sessao de fotos.',
                ],
            ], 402);
        }

        $result = $visionAgent->execute($user, 'Analise esta sessão de fotos corporais do mesmo dia. Retorne JSON com summary e next_recommendations. Não faça diagnóstico médico, não estime percentual de gordura e não identifique a pessoa.', [
            'images' => $photos->map(fn ($photo) => [
                'path' => storage_path('app/public/' . $photo->photo_path),
                'day' => $photo->type,
            ])->values()->all(),
        ]);

        if (!($result['ok'] ?? false)) {
            return response()->json(['error' => ['message' => $result['error'] ?? 'Falha na análise da sessão.']], 500);
        }

        $analysis = $result['structured_data'] ?? json_decode($result['message'] ?? '{}', true);
        EvolutionSessionAnalysis::create([
            'user_id' => $user->id,
            'session_date' => $validated['date'],
            'photo_hash' => $photoHash,
            'analysis' => $analysis,
            'model_name' => $result['model'] ?? null,
            'total_tokens' => $result['tokens'] ?? 0,
            'cost_usd' => $result['cost'] ?? 0,
        ]);

        $aiCredits->consume($user, 'evolution_session_analysis', [
            'source' => 'api_v1',
            'session_date' => $validated['date'],
            'photos_count' => $photos->count(),
        ]);

        return response()->json(['data' => ['analysis' => $analysis, 'cached' => false]]);
    }

    public function report(Request $request, EvolutionReportOrchestratorService $orchestrator): JsonResponse
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return response()->json(['error' => ['message' => 'Relatório inteligente exclusivo para membros Premium.']], 403);
        }

        $aiCredits = app(\App\Services\AiCreditService::class);
        if (! $aiCredits->hasCredits($user, 'evolution_ai_report')) {
            return response()->json([
                'error' => [
                    'code' => 'credits_exceeded',
                    'message' => 'Creditos de IA insuficientes para gerar o relatorio de evolucao.',
                ],
            ], 402);
        }

        $report = $orchestrator->generate($user);
        $aiCredits->consume($user, 'evolution_ai_report', [
            'source' => 'api_v1_direct_report',
        ]);

        return response()->json(['data' => $report]);
    }

    public function requestReport(Request $request, EvolutionReportService $service): JsonResponse
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return response()->json(['error' => ['message' => 'Relatorio inteligente exclusivo para membros Premium.']], 403);
        }

        $request->validate([
            'accept_ai_body_photo_analysis' => ['sometimes', 'boolean'],
        ]);

        $aiCredits = app(\App\Services\AiCreditService::class);
        if (! $aiCredits->hasCredits($user, 'evolution_ai_report')) {
            return response()->json([
                'error' => [
                    'code' => 'credits_exceeded',
                    'message' => 'Creditos de IA insuficientes para solicitar o relatorio de evolucao.',
                ],
            ], 402);
        }

        $report = $service->createRequest($user, $request);
        $aiCredits->consume($user, 'evolution_ai_report', [
            'source' => 'api_v1_report_request',
            'report_id' => $report->id,
        ]);

        return response()->json([
            'data' => [
                'report_id' => $report->id,
                'status' => $report->status,
                'current_session_date' => $report->current_session_date?->toDateString(),
                'previous_session_date' => $report->previous_session_date?->toDateString(),
                'message' => 'Seu relatorio esta sendo preparado.',
            ],
        ], 202);
    }

    public function reportConsent(Request $request, EvolutionReportService $service): JsonResponse
    {
        $consent = $service->latestConsent($request->user());

        return response()->json([
            'data' => [
                'required' => true,
                'type' => EvolutionReportService::CONSENT_TYPE,
                'version' => EvolutionReportService::CONSENT_VERSION,
                'accepted' => $consent !== null,
                'accepted_at' => $consent?->created_at?->toIso8601String(),
                'copy' => [
                    'title' => 'Analise de fotos corporais por IA',
                    'summary' => 'Usamos suas fotos de evolucao apenas para gerar comparacoes visuais conservadoras e metricas de acompanhamento. Nao buscamos identificar voce, diagnosticar condicoes de saude ou estimar percentual de gordura por imagem.',
                ],
            ],
        ]);
    }

    public function showReport(Request $request, EvolutionReport $report): JsonResponse
    {
        abort_unless((int) $report->user_id === (int) $request->user()->id, 404);

        return response()->json([
            'data' => [
                'id' => $report->id,
                'status' => $report->status,
                'current_session_date' => $report->current_session_date?->toDateString(),
                'previous_session_date' => $report->previous_session_date?->toDateString(),
                'confidence' => $report->confidence !== null ? (float) $report->confidence : null,
                'failure_reason' => $report->failure_reason,
                'limited_reason' => $report->limited_reason,
                'final_report' => $report->final_report,
                'started_at' => $report->started_at?->toIso8601String(),
                'completed_at' => $report->completed_at?->toIso8601String(),
                'published_at' => $report->published_at?->toIso8601String(),
            ],
        ]);
    }

    private function photoPayload(EvolutionPhoto $photo): array
    {
        return [
            'id' => $photo->id,
            'type' => $photo->type,
            'registered_date' => (string) $photo->registered_date,
            'weight_kg' => $photo->weight_kg !== null ? (float) $photo->weight_kg : null,
            'media_url' => asset('storage/' . $photo->photo_path),
            'created_at' => $photo->created_at?->toIso8601String(),
        ];
    }

    public function downloadPdf(Request $request, int $id): StreamedResponse|JsonResponse
    {
        $report = EvolutionReport::where('user_id', $request->user()->id)->find($id);

        if (! $report) {
            return response()->json(['message' => 'Relatório de evolução não encontrado.'], 404);
        }

        if (empty($report->pdf_path)) {
            return response()->json(['message' => 'Nenhum PDF associado a este relatório de evolução.'], 404);
        }

        $disk = config('filesystems.default', 'local');
        if (! Storage::disk($disk)->exists($report->pdf_path)) {
            $diskHistorico = config('pdf.historico_disk', 'local');
            if (Storage::disk($diskHistorico)->exists($report->pdf_path)) {
                $disk = $diskHistorico;
            } else {
                return response()->json(['message' => 'Arquivo PDF físico não encontrado no servidor.'], 404);
            }
        }

        $filename = basename($report->pdf_path);

        return Storage::disk($disk)->download($report->pdf_path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
