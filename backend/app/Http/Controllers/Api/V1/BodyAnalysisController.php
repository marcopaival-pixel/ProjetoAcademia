<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BodyAnalysis;
use App\Services\BodyAnalysisProcessingService;
use Illuminate\Http\Request;

class BodyAnalysisController extends Controller
{
    public function index(Request $request, BodyAnalysisProcessingService $processor)
    {
        $analyses = BodyAnalysis::withoutGlobalScopes()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (BodyAnalysis $analysis) => $processor->payload($analysis))
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'analyses' => $analyses,
            ],
        ]);
    }

    public function store(Request $request, BodyAnalysisProcessingService $processor)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'view_type' => 'required|string|in:front,back,side',
            'landmarks' => 'nullable|json',
            'metrics' => 'nullable|json',
        ]);

        $result = $processor->store(
            $request->user(),
            $request->file('photo'),
            $request->view_type,
            json_decode($request->landmarks ?: 'null', true),
            json_decode($request->metrics ?: 'null', true),
        );

        if (!($result['ok'] ?? false)) {
            return response()->json($result['payload'], $result['status']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'analysis' => $processor->payload($result['analysis']),
            ],
        ]);
    }

    public function show(Request $request, int $analysis, BodyAnalysisProcessingService $processor)
    {
        $analysis = BodyAnalysis::withoutGlobalScopes()
            ->whereKey($analysis)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'analysis' => $processor->payload($analysis),
            ],
        ]);
    }

    public function compare(Request $request, BodyAnalysisProcessingService $processor)
    {
        $request->validate([
            'id1' => 'required|integer',
            'id2' => 'required|integer',
        ]);

        $first = BodyAnalysis::withoutGlobalScopes()
            ->whereKey($request->integer('id1'))
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $second = BodyAnalysis::withoutGlobalScopes()
            ->whereKey($request->integer('id2'))
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $processor->comparePayload($first, $second),
        ]);
    }
}
