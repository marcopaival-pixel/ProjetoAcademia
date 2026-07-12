<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\ExerciseCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseCatalogController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $query = ExerciseCatalog::query()
            ->where('is_active', true)
            ->with('muscles:id,name')
            ->orderBy('muscle_group')
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('muscle_group', 'like', "%{$search}%");
            });
        }

        $exercises = $query->limit(200)->get()->map(fn (ExerciseCatalog $exercise): array => [
            'id' => $exercise->id,
            'name' => $exercise->name,
            'muscle_group' => $exercise->muscle_group,
            'equipment' => $exercise->equipment,
            'difficulty' => $exercise->difficulty,
            'muscles' => $exercise->muscles->pluck('name')->values()->all(),
        ])->values()->all();

        return $this->success(['exercises' => $exercises], ['count' => count($exercises)]);
    }
}
