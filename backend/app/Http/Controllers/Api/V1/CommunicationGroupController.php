<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunicationGroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // O usuario possui a relacao communicationGroups() ?
        // Se nao tiver explicitamente no model User, podemos buscar via o model CommunicationGroup e o pivot
        $groups = \App\Models\CommunicationGroup::whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', 'approved');
        })
        ->where('is_active', true)
        ->get()
        ->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
                'description' => $group->description,
                'is_private' => $group->is_private,
                'can_members_send_messages' => $group->can_members_send_messages,
            ];
        });

        return response()->json([
            'data' => [
                'groups' => $groups
            ]
        ]);
    }
}
