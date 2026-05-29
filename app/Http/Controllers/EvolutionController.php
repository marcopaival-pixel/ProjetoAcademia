<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvolutionPhoto;

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
        foreach (['front', 'side', 'back'] as $type) {
            if (isset($photosByType[$type]) && $photosByType[$type]->count() >= 2) {
                $evolutionPhotos[$type] = [
                    'first' => $photosByType[$type]->first(),
                    'last' => $photosByType[$type]->last(),
                ];
            }
        }
            
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

        return view('evolution.index', [
            'photos' => $photos,
            'isPremium' => $isPremium,
            'latestAssessment' => $latestAssessment,
            'assessments' => $processedAssessments->reverse(),
            'chartData' => $chartData,
            'evolutionPhotos' => $evolutionPhotos,
            'healthScore' => $healthScore,
            'user' => $user
        ]);
    }
    
    public function store(Request $request)
    {
        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();

        $request->validate([
            'photo' => 'required|image|max:10240',
            'type' => 'required|in:front,side,back,custom',
            'registered_date' => 'required|date',
            'weight_kg' => 'nullable|numeric'
        ]);

        if (!$isPremium) {
            $photoCount = EvolutionPhoto::where('user_id', $user->id)->count();
            if ($photoCount >= 10) {
                return back()->with('error', 'Você atingiu o limite de 10 fotos do plano Free. Faça upgrade para o NexShape Premium para armazenamento ilimitado.');
            }
        }
        
        $path = $request->file('photo')->store('evolution', 'public');
        
        EvolutionPhoto::create([
            'user_id' => $user->id,
            'photo_path' => $path,
            'type' => $request->type,
            'registered_date' => $request->registered_date,
            'weight_kg' => $request->weight_kg,
            // Guardamos o plano no momento da foto para auditoria futura se necessário
        ]);
        
        return back()->with('success', 'Sua evolução foi registrada com sucesso! Continue o ótimo trabalho.');
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

        $request->validate([
            'photo_id_1' => 'required|exists:evolution_photos,id',
            'photo_id_2' => 'required|exists:evolution_photos,id',
        ]);

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
            return response()->json([
                'success' => true,
                'analysis' => $result['message']
            ]);
        }

        return response()->json(['error' => $result['error'] ?? 'Falha na análise NexShape Vision.'], 500);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $photo = EvolutionPhoto::where('user_id', $user->id)->findOrFail($id);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Registro removido com sucesso.');
    }

    /**
     * Gera o relatório de acompanhamento de evolução do aluno via IA.
     */
    public function aiReport(Request $request, \App\Services\AIFitnessGeneratorService $aiService)
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return back()->with('error', 'Relatório Inteligente exclusivo para membros Premium.');
        }

        $cacheKey = "user_{$user->id}_weekly_ai_report_content";

        try {
            $reportData = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addDays(7), function () use ($aiService, $user) {
                $result = $aiService->generateEvolutionReport($user);
                
                if (!$result['ok']) {
                    throw new \Exception($result['error']);
                }

                $jsonStr = $result['report'];
                // Limpar blocos de markdown caso a IA os coloque (ex: ```json ... ```)
                $jsonStr = preg_replace('/```json/i', '', $jsonStr);
                $jsonStr = preg_replace('/```/', '', $jsonStr);
                $jsonStr = trim($jsonStr);

                $data = json_decode($jsonStr, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                    // Fallback se a IA falhar no JSON
                    throw new \Exception('O assistente devolveu um formato inválido de relatório.');
                }

                return $data;
            });
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
