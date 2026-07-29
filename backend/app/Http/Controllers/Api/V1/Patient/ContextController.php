<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalPatient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContextController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $links = ProfessionalPatient::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'Sim')
            ->with('professional:id,name')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ProfessionalPatient $link) => $this->payload($link))
            ->values();

        return response()->json(['data' => $links]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'context_id' => ['required', 'integer', 'min:1'],
        ]);

        $link = ProfessionalPatient::query()
            ->where('id', $validated['context_id'])
            ->where('user_id', $request->user()->id)
            ->where('status', 'Sim')
            ->with('professional:id,name')
            ->firstOrFail();

        return response()->json([
            'data' => $this->payload($link),
            'message' => 'Contexto ativo confirmado.',
        ]);
    }

    private function payload(ProfessionalPatient $link): array
    {
        return [
            'id' => $link->id,
            'context_id' => $link->id,
            'professional_id' => $link->professional_id,
            'professional_name' => $link->professional?->name,
            'label' => $link->professional?->name ?? 'Acompanhamento clínico',
            'type' => 'clinic',
        ];
    }
}
