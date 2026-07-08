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
            ->map(function (User $professional): array {
                /** @var \App\Models\ProfessionalProfile|null $profile */
                $profile = $professional->professionalProfile;

                /** @var object|null $branding */
                $branding = $professional->branding;

                return [
                    'id' => $professional->getAttribute('id'),
                    'name' => $professional->getAttribute('name'),
                    'email' => $professional->getAttribute('email'),
                    'specialty' => $profile ? ($profile->getAttribute('specialty') ?? null) : null,
                    'service_types' => $profile ? ($profile->getAttribute('service_types') ?? []) : [],
                    'branding' => [
                        'clinic_name' => $branding ? ($branding->clinic_name ?? null) : null,
                        'primary_color' => $branding ? ($branding->primary_color ?? null) : null,
                    ],
                ];
            })
            ->values()
            ->all();

        return $this->success(['professionals' => $professionals]);
    }
}
