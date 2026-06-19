<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use FormatsApiResponses;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing(['roles', 'branding']);

        return $this->success((new UserProfileResource($user))->resolve());
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
        ]);

        if (isset($validated['name'])) {
            $user->update(['name' => $validated['name']]);
        }

        return $this->success([
            'id' => $user->id,
            'name' => $user->fresh()->name,
            'email' => $user->email,
        ]);
    }
}
