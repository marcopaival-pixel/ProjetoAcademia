<?php

namespace App\Services;

use App\Jobs\GenerateEvolutionReport;
use App\Models\EvolutionPhoto;
use App\Models\EvolutionReport;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EvolutionReportService
{
    public const CONSENT_TYPE = AiBodyPhotoConsentService::CONSENT_TYPE;

    public const CONSENT_VERSION = AiBodyPhotoConsentService::CONSENT_VERSION;

    public function __construct(
        private AiBodyPhotoConsentService $bodyPhotoConsent,
    ) {}

    public function createRequest(User $user, ?Request $request = null): EvolutionReport
    {
        $consent = $this->bodyPhotoConsent->ensureConsent($user, $request);
        $sessions = $this->completeSessionDates($user);

        $report = EvolutionReport::create([
            'user_id' => $user->id,
            'consent_id' => $consent->id,
            'current_session_date' => $sessions[0] ?? null,
            'previous_session_date' => $sessions[1] ?? null,
            'status' => EvolutionReport::STATUS_PENDING,
            'provider' => config('ai_evolution.provider', config('services.openai.model') ? 'openai' : null),
            'prompt_version' => 'evolution-report-orchestrator:v2',
            'schema_version' => 'evolution-report:v1',
        ]);

        GenerateEvolutionReport::dispatch($report->id);

        return $report;
    }

    public function latestConsent(User $user): ?UserConsent
    {
        return $this->bodyPhotoConsent->latestConsent($user);
    }

    public function recordConsent(User $user, ?Request $request = null): UserConsent
    {
        return $this->bodyPhotoConsent->recordConsent($user, $request);
    }

    private function completeSessionDates(User $user): array
    {
        return EvolutionPhoto::query()
            ->where('user_id', $user->id)
            ->whereIn('type', ['front', 'back', 'right_side', 'left_side', 'side'])
            ->orderByDesc('registered_date')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn (EvolutionPhoto $photo) => Carbon::parse($photo->registered_date)->toDateString())
            ->filter(function ($photos) {
                $normalizedTypes = $photos->pluck('type')
                    ->flatMap(fn (string $type) => $type === 'side' ? ['right_side', 'left_side'] : [$type])
                    ->unique()
                    ->values()
                    ->all();

                return collect(['front', 'back', 'right_side', 'left_side'])
                    ->every(fn (string $type) => in_array($type, $normalizedTypes, true));
            })
            ->keys()
            ->values()
            ->all();
    }
}
