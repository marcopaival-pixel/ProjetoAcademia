<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvolutionPhoto;
use App\Models\EvolutionSessionAnalysis;
use App\Services\BodyPhotoValidationService;

class EvolutionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();
        
        $query = EvolutionPhoto::where('user_id', $user->id);
        
        if (!$isPremium) {
            // Histórico limitado aos últimos 30 dias no plano Free para visualização
            $query->where('registered_date', '>=', now()->subDays(30));
        }

        $photos = $query->orderBy('registered_date', 'desc')
            ->get()
            ->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->registered_date)->format('Y-m');
            });

        // Agrupamento por tipo para o Antes & Depois Premium
        $photosByType = EvolutionPhoto::where('user_id', $user->id)
            ->orderBy('registered_date', 'asc')
            ->get()
            ->groupBy('type');

        $evolutionPhotos = [];
        foreach (['front', 'right_side', 'left_side', 'side', 'back'] as $type) {
            if (isset($photosByType[$type]) && $photosByType[$type]->count() >= 2) {
                $evolutionPhotos[$type] = [
                    'first' => $photosByType[$type]->first(),
                    'last' => $photosByType[$type]->last(),
                ];
            }
        }

        $todayPhotoTypes = EvolutionPhoto::where('user_id', $user->id)
            ->whereDate('registered_date', now()->toDateString())
            ->pluck('type')
            ->unique()
            ->values()
            ->all();

        $guidedPhotoChecklist = [
            ['type' => 'front', 'label' => 'Frente', 'sent' => in_array('front', $todayPhotoTypes, true)],
            ['type' => 'back', 'label' => 'Costas', 'sent' => in_array('back', $todayPhotoTypes, true)],
            ['type' => 'right_side', 'label' => 'Lado Direito', 'sent' => in_array('right_side', $todayPhotoTypes, true) || in_array('side', $todayPhotoTypes, true)],
            ['type' => 'left_side', 'label' => 'Lado Esquerdo', 'sent' => in_array('left_side', $todayPhotoTypes, true) || in_array('side', $todayPhotoTypes, true)],
        ];

        $photoSessions = EvolutionPhoto::where('user_id', $user->id)
            ->orderBy('registered_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn ($photo) => \Carbon\Carbon::parse($photo->registered_date)->format('Y-m-d'))
            ->map(function ($dayPhotos, $date) {
                $byType = $dayPhotos->groupBy('type');
                $qualityPoints = $dayPhotos->sum(function ($photo) {
                    $notes = json_decode((string) $photo->notes, true) ?: [];
                    $quality = data_get($notes, 'validation.checks.quality');

                    return match ($quality) {
                        'excellent' => 2,
                        'good' => 1,
                        default => 0,
                    };
                });
                $qualityAverage = $dayPhotos->count() > 0 ? $qualityPoints / $dayPhotos->count() : 0;
                $completion = collect(['front', 'back', 'right_side', 'left_side'])
                    ->filter(fn ($type) => $byType->has($type) || ($type !== 'front' && $type !== 'back' && $byType->has('side')))
                    ->count();
                $consistency = match (true) {
                    $completion >= 4 && $qualityAverage >= 1 => ['label' => 'Completa', 'tone' => 'emerald'],
                    $completion >= 2 && $qualityAverage >= 0.5 => ['label' => 'Parcial', 'tone' => 'blue'],
                    default => ['label' => 'Fraca', 'tone' => 'amber'],
                };

                return [
                    'date' => $date,
                    'label' => \Carbon\Carbon::parse($date)->format('d/m/Y'),
                    'completion' => $completion,
                    'consistency' => $consistency,
                    'photos' => [
                        'front' => $byType->get('front')?->first(),
                        'back' => $byType->get('back')?->first(),
                        'right_side' => $byType->get('right_side')?->first() ?? $byType->get('side')?->first(),
                        'left_side' => $byType->get('left_side')?->first() ?? $byType->get('side')?->skip(1)->first() ?? $byType->get('side')?->first(),
                    ],
                ];
            })
            ->take(6)
            ->values();
            
        $assessments = \App\Models\BodyAssessment::where('user_id', $user->id)
            ->orderBy('assessment_date', 'asc')
            ->get();

        $processedAssessments = collect();
        $chartData = [
            'dates' => [],
            'weight' => [],
            'bf' => [],
        ];

        foreach ($assessments as $index => $assessment) {
            $prev = $assessments->get($index - 1);
            $assessment->delta_weight = $prev ? $assessment->weight_kg - $prev->weight_kg : 0;
            $assessment->delta_bf = $prev ? $assessment->bf_percent - $prev->bf_percent : 0;
            $processedAssessments->push($assessment);
            
            $chartData['dates'][] = $assessment->assessment_date->format('d/m/y');
            $chartData['weight'][] = (float) $assessment->weight_kg;
            $chartData['bf'][] = (float) $assessment->bf_percent;
        }

        $latestAssessment = $processedAssessments->last();
        $healthScore = $user->health_score ?? 0;
        $hasCompleteEvolutionRecord = $photoSessions->contains(fn ($session) => ($session['completion'] ?? 0) >= 4);

        return view('evolution.index', [
            'photos' => $photos,
            'isPremium' => $isPremium,
            'hasCompleteEvolutionRecord' => $hasCompleteEvolutionRecord,
            'latestAssessment' => $latestAssessment,
            'assessments' => $processedAssessments->reverse(),
            'chartData' => $chartData,
            'evolutionPhotos' => $evolutionPhotos,
            'guidedPhotoChecklist' => $guidedPhotoChecklist,
            'photoSessions' => $photoSessions,
            'healthScore' => $healthScore,
            'user' => $user
        ]);
    }
    
    public function store(Request $request)
    {
        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'type' => 'required|in:front,side,right_side,left_side,back,custom',
            'registered_date' => 'required|date',
            'weight_kg' => 'nullable|numeric'
        ]);

        if (!$isPremium) {
            $photoCount = EvolutionPhoto::where('user_id', $user->id)->count();
            if ($photoCount >= 10) {
                return back()->with('error', 'Você atingiu o limite de 10 fotos do plano Free. Faça upgrade para o NexShape Premium para armazenamento ilimitado.');
            }
        }
        $photoValidation = app(BodyPhotoValidationService::class)->validate($user, $request->file('photo'));
        if (!($photoValidation['approved'] ?? false)) {
            return back()->with('error', implode(' ', $photoValidation['messages'] ?? ['A foto enviada não foi aprovada.']));
        }

        $path = $request->file('photo')->store('evolution', 'public');
        
        EvolutionPhoto::create([
            'user_id' => $user->id,
            'photo_path' => $path,
            'type' => $request->type,
            'registered_date' => $request->registered_date,
            'weight_kg' => $request->weight_kg,
            'notes' => json_encode([
                'source' => 'evolution_manual_upload',
                'validation' => $photoValidation,
            ], JSON_UNESCAPED_UNICODE),
            // Guardamos o plano no momento da foto para auditoria futura se necessário
        ]);
        
        $this->forgetEvolutionReportCache($user->id);

        return back()->with('success', 'Sua evolução foi registrada com sucesso! Continue o ótimo trabalho.');
    }

    public function validatePhoto(Request $request, BodyPhotoValidationService $validator)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        return response()->json($validator->validate($request->user(), $request->file('photo')));
    }

    /**
     * NexShape Vision: Análise de evolução entre duas fotos usando IA.
     */
    public function analyze(Request $request, \App\Services\AI\OrchestratorService $orchestrator)
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return response()->json(['error' => 'Funcionalidade exclusiva para membros Premium.'], 403);
        }
        $aiCredits = app(\App\Services\AiCreditService::class);

        $request->validate([
            'photo_id_1' => 'required|exists:evolution_photos,id',
            'photo_id_2' => 'required|exists:evolution_photos,id',
        ]);

        if (! $aiCredits->hasCredits($user, 'evolution_photo_comparison')) {
            return response()->json([
                'code' => 'credits_exceeded',
                'error' => 'Creditos de IA insuficientes para analisar a evolucao.',
            ], 402);
        }

        $photo1 = EvolutionPhoto::where('user_id', $user->id)->findOrFail($request->photo_id_1);
        $photo2 = EvolutionPhoto::where('user_id', $user->id)->findOrFail($request->photo_id_2);

        $prompt = "Analise as mudanças físicas entre estas duas fotos (Foto 1: {$photo1->registered_date}, Foto 2: {$photo2->registered_date}).";

        $result = $orchestrator->run($user, $prompt, [
            'intent' => 'clinical',
            'type' => 'evolution_analysis',
            'clinicId' => $user->academy_company_id,
            'photo_1_url' => asset('storage/' . $photo1->photo_path),
            'photo_2_url' => asset('storage/' . $photo2->photo_path),
        ]);

        if ($result['status'] === 'success') {
            $aiCredits->consume($user, 'evolution_photo_comparison', [
                'photo_id_1' => $photo1->id,
                'photo_id_2' => $photo2->id,
            ]);

            return response()->json([
                'success' => true,
                'analysis' => $result['message']
            ]);
        }

        return response()->json(['error' => $result['error'] ?? 'Falha na análise NexShape Vision.'], 500);
    }

    public function analyzeSession(Request $request, \App\Services\AI\Agents\VisionAgent $visionAgent)
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return response()->json(['error' => 'Funcionalidade exclusiva para membros Premium.'], 403);
        }
        $aiCredits = app(\App\Services\AiCreditService::class);

        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $photos = EvolutionPhoto::where('user_id', $user->id)
            ->whereDate('registered_date', $validated['date'])
            ->whereIn('type', ['front', 'back', 'right_side', 'left_side', 'side'])
            ->get();

        if ($photos->count() < 2) {
            return response()->json(['error' => 'A sessão precisa de pelo menos dois ângulos para análise.'], 422);
        }

        $photoHash = hash('sha256', $photos
            ->sortBy('id')
            ->map(fn ($photo) => "{$photo->id}:{$photo->photo_path}:{$photo->updated_at?->timestamp}")
            ->implode('|'));

        $cached = EvolutionSessionAnalysis::where('user_id', $user->id)
            ->whereDate('session_date', $validated['date'])
            ->where('photo_hash', $photoHash)
            ->latest()
            ->first();

        if ($cached) {
            return response()->json([
                'analysis' => $cached->analysis,
                'cached' => true,
            ]);
        }

        if (! $aiCredits->hasCredits($user, 'evolution_session_analysis')) {
            return response()->json([
                'code' => 'credits_exceeded',
                'error' => 'Creditos de IA insuficientes para analisar esta sessao de fotos.',
            ], 402);
        }

        $images = $photos->map(fn ($photo) => [
            'path' => storage_path('app/public/' . $photo->photo_path),
            'day' => $photo->type,
        ])->values()->all();

        $prompt = 'Analise esta sessão de fotos corporais do mesmo dia. Retorne JSON com: summary, positives, attention_points, posture_notes, comparison_quality e next_recommendations. Não faça diagnóstico médico, não estime percentual de gordura e não identifique a pessoa. Foque em qualidade da sessão, postura geral, simetria visual aparente e recomendações para próximas fotos.';

        $result = $visionAgent->execute($user, $prompt, [
            'images' => $images,
        ]);

        if (!($result['ok'] ?? false)) {
            return response()->json(['error' => $result['error'] ?? 'Falha na análise da sessão.'], 500);
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
            'session_date' => $validated['date'],
            'photos_count' => $photos->count(),
        ]);

        return response()->json([
            'analysis' => $analysis,
            'cached' => false,
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $photo = EvolutionPhoto::where('user_id', $user->id)->findOrFail($id);
        $sessionDate = $photo->registered_date;

        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        EvolutionSessionAnalysis::where('user_id', $user->id)
            ->whereDate('session_date', $sessionDate)
            ->delete();

        $this->forgetEvolutionReportCache($user->id);

        return back()->with('success', 'Registro removido com sucesso.');
    }

    private function forgetEvolutionReportCache(int $userId): void
    {
        \Illuminate\Support\Facades\Cache::forget("user_{$userId}_evolution_visual_report_v1");
        \Illuminate\Support\Facades\Cache::forget("user_{$userId}_weekly_ai_report_content");
    }

    /**
     * Gera o relatório de acompanhamento de evolução do aluno via IA.
     */
    public function aiReport(Request $request, \App\Services\AI\EvolutionReportOrchestratorService $orchestrator)
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return back()->with('error', 'Relatório Inteligente exclusivo para membros Premium.');
        }
        $aiCredits = app(\App\Services\AiCreditService::class);

        $cacheKey = "user_{$user->id}_evolution_visual_report_v1";
        $wasCached = \Illuminate\Support\Facades\Cache::has($cacheKey);

        if (! $wasCached && ! $aiCredits->hasCredits($user, 'evolution_ai_report')) {
            return back()->with('error', 'Creditos de IA insuficientes para gerar o relatorio de evolucao.');
        }

        try {
            $reportData = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addDays(7), function () use ($orchestrator, $user) {
                return $orchestrator->generate($user);
            });
            if (! $wasCached) {
                $aiCredits->consume($user, 'evolution_ai_report', [
                    'source' => 'web_ai_report',
                ]);
            }
        } catch (\Exception $e) {
            // Remove do cache caso tenha dado erro antes de salvar, por garantia
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
            return back()->with('error', 'Falha ao gerar o relatório: ' . $e->getMessage());
        }

        return view('evolution.ai-report', [
            'reportData' => $reportData,
            'user' => $user
        ]);
    }
}
