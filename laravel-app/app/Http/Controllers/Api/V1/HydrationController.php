<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\WaterEntry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HydrationController extends Controller
{
    use FormatsApiResponses;

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $date = $request->query('date', now()->toDateString());

        $entries = $user->waterEntries()
            ->whereDate('entry_date', $date)
            ->orderByDesc('drank_at')
            ->get();

        $target = (int) ($profile->water_target_ml ?? 2000);
        $consumed = (int) $entries->sum('amount_ml');
        $expectedNow = $this->expectedConsumptionNow($target, $date);

        return $this->success([
            'date' => $date,
            'target_ml' => $target,
            'consumed_ml' => $consumed,
            'percentage' => $target > 0 ? (int) round(($consumed / $target) * 100) : 0,
            'expected_now_ml' => $expectedNow,
            'status' => $this->statusLabel($consumed, $expectedNow),
            'is_auto' => (bool) $profile->is_water_target_auto,
            'entries' => $entries->map(fn (WaterEntry $entry): array => $this->entryPayload($entry))->values(),
        ], [
            'is_premium' => $user->hasPremiumAccess(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount_ml' => ['required', 'integer', 'min:1', 'max:5000'],
            'source' => ['nullable', 'string', 'max:20'],
            'entry_date' => ['nullable', 'date'],
        ]);

        $entry = $request->user()->waterEntries()->create([
            'entry_date' => $data['entry_date'] ?? now()->toDateString(),
            'drank_at' => now(),
            'amount_ml' => $data['amount_ml'],
            'source' => $data['source'] ?? 'android',
        ]);

        return $this->success($this->entryPayload($entry), status: 201);
    }

    public function destroy(Request $request, WaterEntry $waterEntry): JsonResponse
    {
        if ((int) $waterEntry->user_id !== (int) $request->user()->id) {
            return $this->error('Acesso negado.', 403, 'forbidden');
        }

        $waterEntry->delete();

        return $this->success(['deleted' => true]);
    }

    private function expectedConsumptionNow(int $target, string $date): int
    {
        if ($target <= 0 || $date !== now()->toDateString()) {
            return 0;
        }

        $wakeTime = Carbon::today()->setHour(7);
        $sleepTime = Carbon::today()->setHour(23);
        $now = Carbon::now();

        if ($now->lessThanOrEqualTo($wakeTime)) {
            return 0;
        }

        $totalHours = max(1, $wakeTime->diffInHours($sleepTime));
        $elapsedHours = min($totalHours, $wakeTime->diffInHours($now));

        return (int) round(($target / $totalHours) * $elapsedHours);
    }

    private function statusLabel(int $consumed, int $expectedNow): string
    {
        if ($expectedNow <= 0) {
            return 'on_track';
        }

        if ($consumed >= ($expectedNow * 1.15)) {
            return 'ahead';
        }

        if ($consumed < ($expectedNow * 0.85)) {
            return 'behind';
        }

        return 'on_track';
    }

    /**
     * @return array<string, mixed>
     */
    private function entryPayload(WaterEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'entry_date' => $entry->entry_date?->toDateString(),
            'drank_at' => $entry->drank_at?->toIso8601String(),
            'amount_ml' => (int) $entry->amount_ml,
            'source' => $entry->source,
        ];
    }
}
