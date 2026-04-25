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
            
            $latestAssessment = \App\Models\BodyAssessment::where('user_id', $user->id)
                ->orderBy('assessment_date', 'desc')
                ->first();

        return view('evolution.index', compact('photos', 'isPremium', 'latestAssessment'));
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
    public function analyze(Request $request, \App\Services\AIChatService $aiService)
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

        $prompt = "Aja como um especialista em biomecânica e fisiologia do exercício. Através destas duas fotos de evolução (Foto 1: {$photo1->registered_date}, {$photo1->weight_kg}kg; Foto 2: {$photo2->registered_date}, {$photo2->weight_kg}kg), analise as mudanças físicas. "
                . "Compare postura, densidade muscular e composição visual. Forneça um relatório técnico, motivador e focado em resultados reais encontrados nas imagens. "
                . "IMPORTANTE: Se o tempo entre as fotos for curto, foque em pequenas mudanças de tônus e postura.";

        // Aqui assumimos que o AIChatService pode lidar com contexto de imagens ou fazemos uma análise textual baseada nos dados
        // Para uma implementação real com visão, passaríamos as URLs das imagens para o Gemini Pro Vision.
        $result = $aiService->chat($prompt, [
            'photo_1_url' => asset('storage/' . $photo1->photo_path),
            'photo_2_url' => asset('storage/' . $photo2->photo_path),
            'user_name' => $user->name
        ]);

        if ($result['ok']) {
            return response()->json([
                'success' => true,
                'analysis' => $result['message']
            ]);
        }

        return response()->json(['error' => 'Falha na análise NexShape Vision.'], 500);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $photo = EvolutionPhoto::where('user_id', $user->id)->findOrFail($id);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Registro removido com sucesso.');
    }
}
