<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class AiChatConsentService
{
    public const CONSENT_TYPE = 'ai_chat_health_data';

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
        if ($user->isAdministrator()) {
            return $this->latestConsent($user) ?? $this->recordConsent($user, $request);
        }

        if ($consent = $this->latestConsent($user)) {
            return $consent;
        }

        if ($request && $request->boolean('accept_ai_chat_health_data')) {
            return $this->recordConsent($user, $request);
        }

        throw new HttpResponseException(response()->json([
            'ok' => false,
            'code' => 'ai_chat_health_data_consent_required',
            'error' => 'E necessario aceitar o consentimento para uso de dados de saude e perfil no chat com IA.',
            'consent' => [
                'type' => self::CONSENT_TYPE,
                'version' => self::CONSENT_VERSION,
                'summary' => 'Seus dados de perfil, treino e nutricao podem ser enviados ao provedor de IA para personalizar respostas do NexBot.',
            ],
        ], 409));
    }

    public function ensureConsentForApi(User $user, ?Request $request = null): UserConsent
    {
        if ($user->isAdministrator()) {
            return $this->latestConsent($user) ?? $this->recordConsent($user, $request);
        }

        if ($consent = $this->latestConsent($user)) {
            return $consent;
        }

        if ($request && $request->boolean('accept_ai_chat_health_data')) {
            return $this->recordConsent($user, $request);
        }

        throw new HttpResponseException(response()->json([
            'message' => 'Consentimento necessario para chat com IA.',
            'code' => 'ai_chat_health_data_consent_required',
            'data' => [
                'consent' => [
                    'type' => self::CONSENT_TYPE,
                    'version' => self::CONSENT_VERSION,
                    'required' => true,
                ],
            ],
        ], 409));
    }
}
