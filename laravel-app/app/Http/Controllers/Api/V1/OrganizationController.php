<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * CRUD para gestão de Tenants (Organizations) do ecossistema NexShape.
 * Permite criar academias, clínicas, clubes e vinculá-los a usuários.
 *
 * Risco: Baixo — cria novos registros, não altera dados legados.
 */
class OrganizationController extends Controller
{
    use FormatsApiResponses;

    /**
     * Lista as organizations às quais o usuário autenticado pertence.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $organizations = $user->organizations()
            ->wherePivot('is_active', true)
            ->get()
            ->map(function (\App\Models\Organization $org) {
                /** @var \Illuminate\Database\Eloquent\Relations\Pivot|null $pivot */
                $pivot = $org->getRelationValue('pivot');

                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'type' => $org->type,
                    'slug' => $org->slug,
                    'role' => $pivot?->getAttribute('role'),
                    'joined_at' => $pivot?->getAttribute('joined_at'),
                ];
            });

        return $this->success($organizations->values()->all());
    }

    /**
     * Cria uma nova Organization (Tenant).
     * Apenas usuários com papel de professional ou admin podem criar.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole(['professional', 'instructor', 'supervisor', 'admin'])) {
            return response()->json(['error' => 'Apenas profissionais podem criar organizações.'], 403);
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'type' => ['required', 'string', 'in:clinic,academy,club,studio,rehabilitation'],
            'description' => ['nullable', 'string', 'max:500'],
            'primary_color' => ['nullable', 'string', 'max:9'],
        ]);

        $name = $validated['name'] ?? $validated['company_name'] ?? null;

        if ($name === null || trim($name) === '') {
            return response()->json(['error' => 'Informe o nome da organizacao.'], 422);
        }

        $organization = Organization::create([
            'name' => $name,
            'type' => $validated['type'],
            'slug' => Str::slug($name).'-'.Str::random(4),
            'primary_color' => $validated['primary_color'] ?? null,
            'settings' => ['description' => $validated['description'] ?? null],
        ]);

        // Vincula o criador como admin da nova organização
        $user->organizations()->attach($organization->id, [
            'role' => 'admin',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return $this->success([
            'id' => $organization->id,
            'name' => $organization->name,
            'slug' => $organization->slug,
            'type' => $organization->type,
        ], 201);
    }

    /**
     * Vincula um usuário a uma Organization com um papel específico.
     * Só pode ser feito por um admin da organização.
     */
    public function attachUser(Request $request, Organization $organization): JsonResponse
    {
        $admin = $request->user();

        // Verifica se o solicitante é admin desta org
        $isAdmin = $admin->organizations()
            ->where('organization_id', $organization->id)
            ->wherePivot('role', 'admin')
            ->wherePivot('is_active', true)
            ->exists();

        if (! $isAdmin && ! $admin->hasRole('admin')) {
            return response()->json(['error' => 'Apenas administradores da organização podem vincular usuários.'], 403);
        }

        $validated = $request->validate([
            'user_email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', 'string', 'in:professional,paciente,admin,instructor'],
        ]);

        $target = User::where('email', $validated['user_email'])->first();

        $target->organizations()->syncWithoutDetaching([
            $organization->id => [
                'role' => $validated['role'],
                'is_active' => true,
                'joined_at' => now(),
            ],
        ]);

        return $this->success(['message' => "Usuário {$target->name} vinculado com sucesso."]);
    }
}
