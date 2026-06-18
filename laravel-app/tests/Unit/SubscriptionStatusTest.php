<?php

namespace Tests\Unit;

use App\Support\SubscriptionStatus;
use PHPUnit\Framework\TestCase;

class SubscriptionStatusTest extends TestCase
{
    public function test_normalizes_legacy_portuguese_statuses(): void
    {
        $this->assertSame(SubscriptionStatus::ACTIVE, SubscriptionStatus::normalize('ATIVO'));
        $this->assertSame(SubscriptionStatus::PENDING, SubscriptionStatus::normalize('PENDENTE'));
        $this->assertSame(SubscriptionStatus::OVERDUE, SubscriptionStatus::normalize('ATRASADO'));
        $this->assertSame(SubscriptionStatus::SUSPENDED, SubscriptionStatus::normalize('SUSPENSO'));
        $this->assertSame(SubscriptionStatus::BLOCKED, SubscriptionStatus::normalize('BLOQUEADO'));
        $this->assertSame(SubscriptionStatus::CANCELLED, SubscriptionStatus::normalize('CANCELADO'));
    }

    public function test_premium_access_for_active_and_scheduled_cancel(): void
    {
        $this->assertTrue(SubscriptionStatus::grantsPremiumAccess(SubscriptionStatus::ACTIVE, now()->addWeek()));
        $this->assertTrue(SubscriptionStatus::grantsPremiumAccess(SubscriptionStatus::CANCELLED_SCHEDULED, now()->addWeek()));
        $this->assertFalse(SubscriptionStatus::grantsPremiumAccess(SubscriptionStatus::SUSPENDED, now()->addWeek()));
        $this->assertFalse(SubscriptionStatus::grantsPremiumAccess(SubscriptionStatus::ACTIVE, now()->subDay()));
    }

    public function test_expand_statuses_includes_legacy_aliases(): void
    {
        $expanded = SubscriptionStatus::expandStatuses([SubscriptionStatus::ACTIVE]);

        $this->assertContains('ATIVO', $expanded);
        $this->assertContains('active', $expanded);
    }
}
