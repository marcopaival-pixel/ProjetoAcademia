<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('roles');
        $roles = $user->roles->pluck('name')->values()->all();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles,
                'is_premium' => method_exists($user, 'hasPremiumAccess')
                    ? $user->hasPremiumAccess()
                    : (bool) $user->is_premium,
                'is_student' => in_array('aluno', $roles, true) || in_array('student', $roles, true),
                'is_professional' => method_exists($user, 'isProfessional')
                    ? $user->isProfessional()
                    : count(array_intersect($roles, ['professional', 'instructor', 'supervisor'])) > 0,
                'panels' => $roles,
                'clinic_id' => $user->clinic_id,
                'academy_company_id' => $user->academy_company_id,
                'status' => $user->status,
                'student_status' => $user->student_status ?? null,
            ],
        ]);
    }
}
