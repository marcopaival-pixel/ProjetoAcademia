<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\ClinicProtocol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalProtocolController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $professional = $request->user();

        $protocols = ClinicProtocol::query()
            ->where('academy_company_id', $professional->academy_company_id)
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->orderBy('name')
            ->get()
            ->map(fn (ClinicProtocol $protocol): array => [
                'id' => $protocol->id,
                'name' => $protocol->name,
                'type' => $protocol->type,
                'description' => $protocol->description,
                'objective' => $protocol->objective,
                'frequency' => $protocol->frequency,
                'duration' => $protocol->duration,
            ])
            ->values()
            ->all();

        return $this->success(['protocols' => $protocols], ['count' => count($protocols)]);
    }
}
