<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Status canônicos de assinatura SaaS (valor persistido em subscriptions.status).
 */
final class SubscriptionStatus
{
    public const TRIALING = 'trialing';

    public const ACTIVE = 'active';

    public const PENDING = 'pending';

    public const OVERDUE = 'overdue';

    public const SUSPENDED = 'suspended';

    public const BLOCKED = 'blocked';

    public const CANCELLED_SCHEDULED = 'cancelled_scheduled';

    public const CANCELLED = 'cancelled';

    public const EXPIRED = 'expired';

    public const DECLINED = 'declined';

    /** @var array<string, list<string>> */
    private const LEGACY_ALIASES = [
        self::TRIALING => ['trialing', 'trial', 'AGUARDANDO'],
        self::ACTIVE => ['active', 'ATIVO', 'FIN_ATIVO'],
        self::PENDING => ['pending', 'PENDENTE', 'FIN_PENDENTE', 'PENDENTE_APROVACAO'],
        self::OVERDUE => ['overdue', 'ATRASADO', 'FIN_ATRASADO'],
        self::SUSPENDED => ['suspended', 'SUSPENSO', 'FIN_SUSPENSO'],
        self::BLOCKED => ['blocked', 'BLOQUEADO', 'FIN_BLOQUEADO'],
        self::CANCELLED_SCHEDULED => ['cancelled_scheduled'],
        self::CANCELLED => ['cancelled', 'canceled', 'CANCELADO'],
        self::EXPIRED => ['expired'],
        self::DECLINED => ['declined', 'RECUSADO'],
    ];

    /** @var array<string, string> */
    private static array $legacyLookup;

    public static function normalize(?string $raw): string
    {
        if ($raw === null || trim($raw) === '') {
            return self::PENDING;
        }

        $key = strtolower(trim($raw));

        if (isset(self::legacyLookup()[$key])) {
            return self::legacyLookup()[$key];
        }

        $upper = strtoupper(trim($raw));
        if (isset(self::legacyLookup()[$upper])) {
            return self::legacyLookup()[$upper];
        }

        if (in_array($raw, self::allCanonical(), true)) {
            return $raw;
        }

        return self::PENDING;
    }

    /**
     * @return list<string>
     */
    public static function allCanonical(): array
    {
        return [
            self::TRIALING,
            self::ACTIVE,
            self::PENDING,
            self::OVERDUE,
            self::SUSPENDED,
            self::BLOCKED,
            self::CANCELLED_SCHEDULED,
            self::CANCELLED,
            self::EXPIRED,
            self::DECLINED,
        ];
    }

    /**
     * @param  list<string>  $canonical
     * @return list<string>
     */
    public static function expandStatuses(array $canonical): array
    {
        $expanded = [];

        foreach ($canonical as $status) {
            $expanded = array_merge(
                $expanded,
                self::LEGACY_ALIASES[$status] ?? [$status]
            );
        }

        return array_values(array_unique($expanded));
    }

    /**
     * @param  list<string>  $canonical
     */
    public static function scopeWhereStatusIn(Builder $query, array $canonical): Builder
    {
        return $query->whereIn('status', self::expandStatuses($canonical));
    }

    public static function label(string $canonical): string
    {
        return match (self::normalize($canonical)) {
            self::TRIALING => 'Trial',
            self::ACTIVE => 'Ativo',
            self::PENDING => 'Pendente',
            self::OVERDUE => 'Inadimplente',
            self::SUSPENDED => 'Suspenso',
            self::BLOCKED => 'Bloqueado',
            self::CANCELLED_SCHEDULED => 'Cancelamento agendado',
            self::CANCELLED => 'Cancelado',
            self::EXPIRED => 'Expirado',
            self::DECLINED => 'Recusado',
            default => ucfirst($canonical),
        };
    }

    /**
     * Status que mantêm acesso premium (desde que end_date permita).
     *
     * @return list<string>
     */
    public static function premiumEligible(): array
    {
        return [
            self::ACTIVE,
            self::TRIALING,
            self::CANCELLED_SCHEDULED,
        ];
    }

    /**
     * @return list<string>
     */
    public static function mrrEligible(): array
    {
        return [
            self::ACTIVE,
            self::TRIALING,
            self::CANCELLED_SCHEDULED,
        ];
    }

    /**
     * @return list<string>
     */
    public static function delinquent(): array
    {
        return [
            self::OVERDUE,
            self::SUSPENDED,
            self::BLOCKED,
        ];
    }

    /**
     * @return list<string>
     */
    public static function terminal(): array
    {
        return [
            self::CANCELLED,
            self::EXPIRED,
            self::DECLINED,
        ];
    }

    public static function grantsPremiumAccess(string $rawStatus, $endDate = null): bool
    {
        $status = self::normalize($rawStatus);

        if (! in_array($status, self::premiumEligible(), true)) {
            return false;
        }

        if ($endDate === null) {
            return true;
        }

        if ($endDate instanceof \DateTimeInterface) {
            return $endDate >= now()->startOfDay();
        }

        try {
            return \Carbon\Carbon::parse($endDate)->startOfDay() >= now()->startOfDay();
        } catch (\Throwable) {
            return true;
        }
    }

    /**
     * @return array<string, string>
     */
    private static function legacyLookup(): array
    {
        if (isset(self::$legacyLookup)) {
            return self::$legacyLookup;
        }

        self::$legacyLookup = [];

        foreach (self::LEGACY_ALIASES as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                self::$legacyLookup[strtolower($alias)] = $canonical;
                self::$legacyLookup[strtoupper($alias)] = $canonical;
            }
            self::$legacyLookup[$canonical] = $canonical;
        }

        return self::$legacyLookup;
    }
}
