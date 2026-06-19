<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EvolutionPhotoResource;
use App\Models\EvolutionPhoto;
use App\Services\SecureFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvolutionPhotoController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = EvolutionPhoto::query()->where('user_id', $user->id);

        if (! $user->hasPremiumAccess()) {
            $query->where('registered_date', '>=', now()->subDays(30));
        }

        $photos = $query->orderByDesc('registered_date')->get();

        return $this->success([
            'photos' => EvolutionPhotoResource::collection($photos)->resolve(),
        ], [
            'is_premium' => $user->hasPremiumAccess(),
            'count' => $photos->count(),
        ]);
    }

    public function store(Request $request, SecureFileService $secureFiles): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
            'type' => ['required', 'in:front,side,back,custom'],
            'registered_date' => ['required', 'date'],
            'weight_kg' => ['nullable', 'numeric'],
        ]);

        if (! $user->hasPremiumAccess()) {
            $photoCount = EvolutionPhoto::where('user_id', $user->id)->count();
            if ($photoCount >= 10) {
                return $this->error(
                    'Limite de 10 fotos do plano Free atingido.',
                    403,
                    'plan_limit_reached'
                );
            }
        }

        $path = $secureFiles->storeSensitiveFile($request->file('photo'), 'evolution');

        $photo = EvolutionPhoto::create([
            'user_id' => $user->id,
            'photo_path' => $path,
            'type' => $validated['type'],
            'registered_date' => $validated['registered_date'],
            'weight_kg' => $validated['weight_kg'] ?? null,
        ]);

        return $this->success((new EvolutionPhotoResource($photo))->resolve(), status: 201);
    }

    public function destroy(Request $request, EvolutionPhoto $photo): JsonResponse
    {
        if ((int) $photo->user_id !== (int) $request->user()->id) {
            return $this->error('Acesso negado.', 403, 'forbidden');
        }

        app(SecureFileService::class)->deleteFile($photo->photo_path);
        $photo->delete();

        return $this->success(['deleted' => true]);
    }
}
