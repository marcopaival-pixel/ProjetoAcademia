<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WaterEntry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HydrationController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile ?: $user->profile()->create();
        $date = Carbon::parse($request->query('date', today()->toDateString()))->toDateString();

        $entries = $user->waterEntries()
            ->whereDate('entry_date', $date)
            ->orderByDesc('drank_at')
            ->get();

        $target = (int) ($profile->water_target_ml ?? 2000);
        $consumed = (int) $entries->sum('amount_ml');
        $expected = $this->expectedNow($target, $date);

        return response()->json([
            'data' => [
                'date' => $date,
                'target_ml' => $target,
                'consumed_ml' => $consumed,
                'percentage' => $target > 0 ? (int) round(($consumed / $target) * 100) : 0,
                'expected_now_ml' => $expected,
                'status' => $this->statusLabel($consumed, $expected),
                'is_auto' => (bool) ($profile->is_water_target_auto ?? false),
                'entries' => $entries->map(fn (WaterEntry $entry) => $this->entryPayload($entry))->values(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount_ml' => ['required', 'integer', 'min:1', 'max:5000'],
            'source' => ['nullable', 'string', 'max:40'],
            'entry_date' => ['nullable', 'date'],
        ]);

        $entry = $request->user()->waterEntries()->create([
            'entry_date' => Carbon::parse($validated['entry_date'] ?? today()->toDateString())->toDateString(),
            'drank_at' => now(),
            'amount_ml' => $validated['amount_ml'],
            'source' => $validated['source'] ?? 'android',
        ]);

        return response()->json(['data' => $this->entryPayload($entry)], 201);
    }

    public function destroy(Request $request, WaterEntry $entry): JsonResponse
    {
        abort_unless((int) $entry->user_id === (int) $request->user()->id, 403);

        $entry->delete();

        return response()->json(['data' => ['deleted' => true]]);
    }

    private function expectedNow(int $target, string $date): int
    {
        if ($date !== today()->toDateString()) {
            return $target;
        }

        $wakeTime = today()->setHour(7);
        $sleepTime = today()->setHour(23);
        $now = now();

        if ($now->lessThanOrEqualTo($wakeTime)) {
            return 0;
        }

        $totalHours = max($wakeTime->diffInHours($sleepTime), 1);
        $elapsedHours = min($totalHours, $wakeTime->diffInHours($now));

        return (int) round(($target / $totalHours) * $elapsedHours);
    }

    private function statusLabel(int $consumed, int $expected): string
    {
        if ($expected <= 0 || $consumed >= $expected * 1.15) {
            return 'ahead';
        }

        return $consumed < $expected * 0.85 ? 'behind' : 'on_track';
    }

    private function entryPayload(WaterEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'entry_date' => optional($entry->entry_date)->toDateString(),
            'drank_at' => optional($entry->drank_at)->toISOString(),
            'amount_ml' => (int) $entry->amount_ml,
            'source' => $entry->source,
        ];
    }
}
