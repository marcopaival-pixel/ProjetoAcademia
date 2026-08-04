<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class AiBodyPhotoConsentService
{
    public const CONSENT_TYPE = 'ai_body_photo_analysis';

    public const CONSENT_VERSION = '1.0';

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

    public function ensureConsent(User $user, ?Request $request = null): UserConsent
    {
        if ($consent = $this->latestConsent($user)) {
            return $consent;
        }

        if ($request && $request->boolean('accept_ai_body_photo_analysis')) {
            return $this->recordConsent($user, $request);
        }

        throw new HttpResponseException(response()->json([
            'success' => false,
            'code' => 'ai_body_photo_analysis_consent_required',
            'error' => [
                'code' => 'ai_body_photo_analysis_consent_required',
                'message' => 'E necessario aceitar o consentimento especifico para analise de fotos corporais por IA.',
            ],
        ], 409));
    }
}
