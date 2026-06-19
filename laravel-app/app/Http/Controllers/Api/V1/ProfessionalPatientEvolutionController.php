<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\ResolvesProfessionalPatient;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EvolutionPhotoResource;
use App\Models\EvolutionPhoto;
use App\Services\SecureFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalPatientEvolutionController extends Controller
{
    use FormatsApiResponses;
    use ResolvesProfessionalPatient;

    public function index(Request $request, int $patient): JsonResponse
    {
        $this->linkedPatient($request, $patient);

        $photos = EvolutionPhoto::withoutGlobalScopes()
            ->where('user_id', $patient)
            ->orderByDesc('registered_date')
            ->get();

        return $this->success([
            'photos' => EvolutionPhotoResource::collection($photos)->resolve(),
        ], ['count' => $photos->count()]);
    }

    public function store(Request $request, int $patient, SecureFileService $secureFiles): JsonResponse
    {
        $this->linkedPatient($request, $patient);

        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
            'type' => ['required', 'in:front,side,back,custom'],
            'registered_date' => ['required', 'date'],
            'weight_kg' => ['nullable', 'numeric'],
        ]);

        $path = $secureFiles->storeSensitiveFile($request->file('photo'), 'evolution');

        $photo = EvolutionPhoto::create([
            'user_id' => $patient,
            'photo_path' => $path,
            'type' => $validated['type'],
            'registered_date' => $validated['registered_date'],
            'weight_kg' => $validated['weight_kg'] ?? null,
        ]);

        return $this->success((new EvolutionPhotoResource($photo))->resolve(), status: 201);
    }
}
