<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalPatient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $links = ProfessionalPatient::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'Sim')
            ->with('professional:id,name')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ProfessionalPatient $link) => [
                'id' => $link->id,
                'professional_id' => $link->professional_id,
                'professional_name' => $link->professional?->name,
                'status' => $link->status,
                'linked_at' => optional($link->data_cadastro ?? $link->created_at)->toDateString(),
            ])
            ->values();

        return response()->json(['data' => $links]);
    }
}
