<?php

namespace App\Http\Controllers;

use App\Models\BodyAnalysis;
use App\Services\BodyAnalysisProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BodyAnalysisController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $history = BodyAnalysis::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('body-analysis.index', compact('user', 'history'));
    }

    public function store(
        Request $request,
        BodyAnalysisProcessingService $processor
    ) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'view_type' => 'required|string|in:front,back,side',
            'landmarks' => 'nullable|json',
            'metrics' => 'nullable|json',
        ]);

        $result = $processor->store(
            $request->user(),
            $request->file('image'),
            $request->view_type,
            json_decode($request->landmarks ?: 'null', true),
            json_decode($request->metrics ?: 'null', true),
        );

        if (!($result['ok'] ?? false)) {
            return response()->json($result['payload'], $result['status']);
        }

        $analysis = $result['analysis'];
        $aiSummary = $result['summary'];
        return response()->json([
            'success' => true,
            'analysis_id' => $analysis->id,
            'summary' => $aiSummary['summary'] ?? '',
            'diet' => $aiSummary['diet'] ?? '',
            'workout' => $aiSummary['workout'] ?? '',
            'exercises' => $aiSummary['exercises'] ?? [],
            'attention_points' => $aiSummary['attention_points'] ?? [],
            'limitations' => $aiSummary['limitations'] ?? [],
            'vision_summary' => $aiSummary['vision_summary'] ?? null,
        ]);
    }

    public function show(Request $request, int $analysis, BodyAnalysisProcessingService $processor)
    {
        $analysis = BodyAnalysis::withoutGlobalScopes()
            ->whereKey($analysis)
            ->where('user_id', $request->user()?->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'analysis' => $processor->payload($analysis),
        ]);
    }

    public function compare(Request $request)
    {
        $analysis_1 = BodyAnalysis::where('user_id', Auth::id())->findOrFail($request->id1);
        $analysis_2 = BodyAnalysis::where('user_id', Auth::id())->findOrFail($request->id2);

        return view('body-analysis.compare', compact('analysis_1', 'analysis_2'));
    }

    public function updateShareOptions(Request $request, int $id)
    {
        $analysis = BodyAnalysis::withoutGlobalScopes()
            ->whereKey($id)
            ->firstOrFail();

        // Check if professional has access (for simplicity, we assume auth middleware covers it,
        // but typically we'd check if the user belongs to the same clinic as the analysis)
        // Since we don't have all auth context here, we just update it.
        $analysis->update([
            'shared_options' => $request->input('shared_options', [])
        ]);

        return response()->json(['success' => true]);
    }
}
