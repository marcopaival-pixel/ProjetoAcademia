<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use App\Models\EvolutionPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvolutionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $patient = auth()->user();
        $context = $request->attributes->get('active_patient_context') ?? $request->attributes->get('active_patient_link');
        $professionalId = $context->professional_id;

        $assessments = BodyAssessment::where('user_id', $patient->id)
            ->where('professional_id', $professionalId)
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

        // Fotos de Evolução (Análise Corporal)
        $photos = \App\Models\BodyAnalysis::where('user_id', $patient->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('view_type');

        $evolutionPhotos = [];
        foreach (['front', 'side', 'back'] as $type) {
            if (isset($photos[$type]) && $photos[$type]->count() >= 2) {
                $first = $photos[$type]->first();
                $last = $photos[$type]->last();
                
                $evolutionPhotos[$type] = [
                    'first' => [
                        'id' => $first->id,
                        'url' => asset('storage/' . $first->image_path),
                        'date' => $first->created_at->toDateString(),
                    ],
                    'last' => [
                        'id' => $last->id,
                        'url' => asset('storage/' . $last->image_path),
                        'date' => $last->created_at->toDateString(),
                    ],
                ];
            }
        }

        return response()->json([
            'data' => [
                'assessments' => $processedAssessments->reverse()->values(),
                'latest' => $processedAssessments->last(),
                'chart_data' => $chartData,
                'evolution_photos' => $evolutionPhotos,
            ]
        ]);
    }
}
