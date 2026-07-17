<?php

namespace App\Support;

class SubscriptionStatus
{
    public static function premiumEligible(): array
    {
        return [
            'active',
            'cancelled_scheduled',
            'ATIVO',
        ];
    }
}
