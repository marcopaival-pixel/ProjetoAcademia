<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentProfessionalController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $professionals = $user->professionals()
            ->with(['professionalProfile', 'branding'])
            ->wherePivot('status', 'Sim')
            ->get()
            ->map(fn (User $professional): array => [
                'id' => $professional->id,
                'name' => $professional->name,
                'email' => $professional->email,
                'specialty' => $professional->professionalProfile?->specialty,
                'service_types' => $professional->professionalProfile?->service_types ?? [],
                'branding' => [
                    'clinic_name' => $professional->branding?->clinic_name,
                    'primary_color' => $professional->branding?->primary_color,
                ],
            ])
            ->values()
            ->all();

        return $this->success(['professionals' => $professionals]);
    }
}
