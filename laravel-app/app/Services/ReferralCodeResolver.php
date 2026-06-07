<?php

namespace App\Services;

use App\Models\CommercialProposal;
use App\Models\Plan;
use App\Models\ReferralCode;
use App\Models\RepresentativeProfile;

class ReferralCodeResolver
{
    /**
     * Resolve um código de indicação (ReferralCode ou RepresentativeProfile).
     *
     * @return array{
     *     source: string,
     *     representative_id: int,
     *     representative_name: string,
     *     discount_amount: float,
     *     discount_rate: float,
     *     plan_id: int|null,
     *     referral_code: ReferralCode|null,
     *     profile: RepresentativeProfile|null
     * }|null
     */
    public function resolve(string $code, ?int $planId = null): ?array
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }

        $referral = ReferralCode::with(['representative', 'commercialProposal'])
            ->where('code', $code)
            ->first();

        if ($referral && $referral->isValid()) {
            $proposal = $referral->commercialProposal;
            $commercialProposal = $proposal instanceof CommercialProposal ? $proposal : null;

            if ($planId !== null && $commercialProposal && (int) $commercialProposal->plan_id !== $planId) {
                return null;
            }

            $valor = $commercialProposal ? (float) $commercialProposal->valor : 0.0;
            $desconto = $commercialProposal ? (float) $commercialProposal->desconto : 0.0;

            return [
                'source' => 'referral_code',
                'representative_id' => (int) $referral->representative_id,
                'representative_name' => $referral->representative->name ?? 'Representante',
                'discount_amount' => $desconto,
                'discount_rate' => $valor > 0 ? round(($desconto / $valor) * 100, 2) : 0.0,
                'plan_id' => $commercialProposal?->plan_id,
                'referral_code' => $referral,
                'profile' => null,
            ];
        }

        $profile = RepresentativeProfile::with('user')->where('code', $code)->first();

        if (! $profile || ! $profile->isValid()) {
            return null;
        }

        $plan = $planId ? Plan::find($planId) : null;
        $discountRate = (float) $profile->max_discount_rate;
        $discountAmount = $plan ? round(((float) $plan->price * $discountRate) / 100, 2) : 0.0;

        return [
            'source' => 'representative_profile',
            'representative_id' => (int) $profile->user_id,
            'representative_name' => $profile->user->name ?? 'Representante',
            'discount_amount' => $discountAmount,
            'discount_rate' => $discountRate,
            'plan_id' => $planId,
            'referral_code' => null,
            'profile' => $profile,
        ];
    }
}
