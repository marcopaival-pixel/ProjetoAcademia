<?php

namespace App\Support;

/**
 * Guarda global para evitar recursividade infinita durante a aplicação
 * dos escopos globais de multi-tenancy.
 */
class TenantGuard
{
    private static bool $isApplying = false;

    public static function isApplying(): bool
    {
        return self::$isApplying;
    }

    public static function setApplying(bool $state): void
    {
        self::$isApplying = $state;
    }

    /**
     * Executa uma função protegendo contra recursividade.
     */
    public static function protect(callable $callback)
    {
        if (self::$isApplying) {
            return null;
        }

        try {
            self::$isApplying = true;
            return $callback();
        } finally {
            self::$isApplying = false;
        }
    }
}
