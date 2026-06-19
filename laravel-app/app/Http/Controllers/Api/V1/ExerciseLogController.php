<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExerciseLogController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $date = $request->query('date', now()->toDateString());

        $entries = DB::table('exercise_entries')
            ->where('user_id', $user->id)
            ->where('entry_date', $date)
            ->orderBy('id')
            ->get();

        return $this->success([
            'date' => $date,
            'entries' => $entries,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'min:0'],
            'entry_date' => ['required', 'date'],
            'activity_type' => ['nullable', 'string', 'max:120'],
            'duration_min' => ['nullable', 'integer', 'min:0'],
            'calories_burned' => ['nullable', 'integer', 'min:0'],
            'rpe' => ['nullable', 'integer', 'min:1', 'max:10'],
            'rest_default' => ['nullable', 'integer', 'min:0'],
            'sets_data' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $id = (int) ($validated['id'] ?? 0);
        $payload = collect($validated)->except(['id'])->all();

        if ($id > 0) {
            $updated = DB::table('exercise_entries')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->update($payload);

            if (! $updated) {
                return $this->error('Registro não encontrado.', 404, 'not_found');
            }

            return $this->success(['id' => $id, 'synced' => true]);
        }

        $newId = DB::table('exercise_entries')->insertGetId(array_merge($payload, [
            'user_id' => $user->id,
            'created_at' => now(),
        ]));

        return $this->success(['id' => $newId, 'synced' => true], status: 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $deleted = DB::table('exercise_entries')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->delete();

        if (! $deleted) {
            return $this->error('Registro não encontrado.', 404, 'not_found');
        }

        return $this->success(['deleted' => true]);
    }
}
