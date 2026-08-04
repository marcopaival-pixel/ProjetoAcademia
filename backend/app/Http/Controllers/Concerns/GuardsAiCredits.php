<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Services\AiCreditService;
use App\Services\MonetizationService;
use Illuminate\Http\JsonResponse;

trait GuardsAiCredits
{
    protected function denyIfInsufficientAiCredits(User $user, string $featureCode, AiCreditService $credits): ?JsonResponse
    {
        if ($user->isAdministrator()) {
            return null;
        }

        $credits->getWallet($user);

        if (! $credits->hasCredits($user, $featureCode)) {
            return response()->json([
                'error' => [
                    'code' => 'credits_exceeded',
                    'message' => 'Creditos de IA insuficientes para esta acao.',
                ],
            ], 402);
        }

        return null;
    }

    protected function denyIfWorkoutImportBlocked(
        User $user,
        MonetizationService $monetization,
        AiCreditService $credits,
    ): ?JsonResponse {
        if ($user->isAdministrator() || $user->hasPremiumAccess()) {
            return $this->denyIfInsufficientAiCredits($user, 'workout_import_photo', $credits);
        }

        $access = $monetization->checkAccess($user, 'workout_import_photo');
        if (! ($access['allowed'] ?? false)) {
            return response()->json([
                'error' => [
                    'code' => 'premium_required',
                    'message' => 'Esta funcionalidade esta disponivel apenas para usuarios Premium.',
                ],
            ], 403);
        }

        return $this->denyIfInsufficientAiCredits($user, 'workout_import_photo', $credits);
    }
}
