<?php

namespace App\Http\Controllers\Api\V1\Professional;

use App\Http\Controllers\Controller;
use App\Models\ClinicProtocol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtocolController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->query('type', 'training');

        $query = ClinicProtocol::query()->with('specialty:id,name');

        if ($user->academy_company_id) {
            $query->where('academy_company_id', $user->academy_company_id);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($type) {
            $query->where('type', $type);
        }

        $protocols = $query->orderBy('name')->get()->map(fn (ClinicProtocol $protocol) => [
            'id' => $protocol->id,
            'name' => $protocol->name,
            'type' => $protocol->type,
            'description' => $protocol->description,
            'objective' => $protocol->objective,
            'frequency' => $protocol->frequency,
            'duration' => $protocol->duration,
            'specialty' => $protocol->specialty?->name,
        ]);

        return response()->json(['data' => ['protocols' => $protocols]]);
    }
}
