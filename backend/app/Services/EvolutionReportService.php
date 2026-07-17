<?php

namespace App\Services;

use App\Jobs\GenerateEvolutionReport;
use App\Models\EvolutionPhoto;
use App\Models\EvolutionReport;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Carbon;

class EvolutionReportService
{
    public const CONSENT_TYPE = 'ai_body_photo_analysis';
    public const CONSENT_VERSION = '1.0';

    public function createRequest(User $user, ?Request $request = null): EvolutionReport
    {
        $consent = $this->resolveConsent($user, $request);
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
        return UserConsent::query()
            ->where('user_id', $user->id)
            ->where('consent_type', self::CONSENT_TYPE)
            ->latest('created_at')
            ->first();
    }

    public function recordConsent(User $user, ?Request $request = null): UserConsent
    {
        return UserConsent::create([
            'user_id' => $user->id,
            'consent_type' => self::CONSENT_TYPE,
            'version' => self::CONSENT_VERSION,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->header('User-Agent'),
        ]);
    }

    private function resolveConsent(User $user, ?Request $request = null): UserConsent
    {
        if ($consent = $this->latestConsent($user)) {
            return $consent;
        }

        if ($request && $request->boolean('accept_ai_body_photo_analysis')) {
            return $this->recordConsent($user, $request);
        }

        throw new HttpResponseException(response()->json([
            'error' => [
                'code' => 'ai_body_photo_analysis_consent_required',
                'message' => 'E necessario aceitar o consentimento especifico para analise de fotos corporais por IA.',
            ],
        ], 409));
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
