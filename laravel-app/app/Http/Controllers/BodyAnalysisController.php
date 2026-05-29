<?php

namespace App\Http\Controllers;

use App\Models\BodyAnalysis;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BodyAnalysisController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user->profile;
        $history = BodyAnalysis::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('body-analysis.index', compact('user', 'profile', 'history'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
            'view_type' => 'required|string|in:front,back,side',
            'landmarks' => 'nullable|json',
            'metrics' => 'nullable|json',
        ]);

        if (!auth()->user()->consumeAiCredit('analyze_body_photo', ['view_type' => $request->view_type])) {
            return response()->json([
                'success' => false,
                'code' => 'credits_exceeded',
                'error' => 'Créditos insuficientes para realizar a análise corporal.'
            ], 403);
        }

        $path = $request->file('image')->store('body-analyses', 'public');

        // Lógica de sugestão simulada baseada nas métricas recebidas (assimetrias, postura)
        $aiSummary = $this->generateAiSummary(json_decode($request->metrics, true));

        $analysis = BodyAnalysis::create([
            'user_id' => Auth::id(),
            'photo_path' => $path,
            'view_type' => $request->view_type,
            'landmarks' => json_decode($request->landmarks),
            'metrics' => json_decode($request->metrics),
            'ai_summary' => $aiSummary,
        ]);

        return response()->json([
            'success' => true,
            'analysis_id' => $analysis->id,
            'summary' => $aiSummary['summary'] ?? '',
            'diet' => $aiSummary['diet'] ?? '',
            'workout' => $aiSummary['workout'] ?? '',
            'exercises' => $aiSummary['exercises'] ?? [],
        ]);
    }

    private function generateAiSummary($metrics)
    {
        $response = [
            'summary' => 'Proporções ótimas detectadas! Continue com o plano atual.',
            'diet' => 'Manter dieta normocalórica com foco em hidratação e ingestão adequada de macronutrientes.',
            'workout' => 'Foco em manutenção muscular e mobilidade. Nenhum exercício corretivo severo necessário.',
            'exercises' => ['Agachamento Livre', 'Desenvolvimento', 'Levantamento Terra']
        ];

        if (empty($metrics)) return $response;

        $summary = [];
        $workout = [];
        $diet = 'Aumentar a ingestão de proteínas magras para apoiar a recuperação muscular (mínimo 1.8g/kg).';
        $exercises = [];

        if (isset($metrics['asymmetry_shoulders']) && $metrics['asymmetry_shoulders'] > 5) {
            $summary[] = "Detectada assimetria significativa nos ombros.";
            $workout[] = "Foco intenso em exercícios unilaterais para equilibrar a musculatura do deltóide.";
            $exercises[] = "Desenvolvimento Arnold Unilateral";
            $exercises[] = "Elevação Lateral com Halter (Lado Fraco Primeiro)";
        }
        if (isset($metrics['posture_score']) && $metrics['posture_score'] < 70) {
            $summary[] = "Sua postura apresenta inclinação excessiva.";
            $workout[] = "Fortaleça o core e a musculatura paravertebral. Melhore o alongamento da cadeia anterior.";
            $exercises[] = "Remada Curvada";
            $exercises[] = "Hiperextensão Lombar";
        }

        if (!empty($summary)) {
            $response['summary'] = implode(' ', $summary);
            $response['workout'] = implode(' ', $workout);
            $response['exercises'] = $exercises;
            $response['diet'] = $diet;
        }

        return $response;
    }

    public function compare(Request $request)
    {
        $analysis_1 = BodyAnalysis::findOrFail($request->id1);
        $analysis_2 = BodyAnalysis::findOrFail($request->id2);

        return view('body-analysis.compare', compact('analysis_1', 'analysis_2'));
    }
}
