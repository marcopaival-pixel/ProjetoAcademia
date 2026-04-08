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
            'summary' => $aiSummary,
        ]);
    }

    private function generateAiSummary($metrics)
    {
        if (empty($metrics)) return "Nenhuma alteração significativa detectada.";

        $summary = [];
        if (isset($metrics['asymmetry_shoulders']) && $metrics['asymmetry_shoulders'] > 5) {
            $summary[] = "Detectada assimetria significativa nos ombros. Foco em exercícios unilaterais.";
        }
        if (isset($metrics['posture_score']) && $metrics['posture_score'] < 70) {
            $summary[] = "Sua postura apresenta inclinação excessiva. Fortaleça o paravertebral.";
        }

        return !empty($summary) ? implode(' ', $summary) : "Proporções ótimas detectadas! Continue com o plano atual.";
    }

    public function compare(Request $request)
    {
        $analysis_1 = BodyAnalysis::findOrFail($request->id1);
        $analysis_2 = BodyAnalysis::findOrFail($request->id2);

        return view('body-analysis.compare', compact('analysis_1', 'analysis_2'));
    }
}
