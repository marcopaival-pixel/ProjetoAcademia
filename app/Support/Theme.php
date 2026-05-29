<?php

namespace App\Support;

class Theme
{
    public const COOKIE = 'projetoacademia_theme';

    public const LIFETIME = 365 * 24 * 60 * 60;

    public static function current(): string
    {
        return 'dark';
    }

    public static function isExplicit(): bool
    {
        return isset($_COOKIE[self::COOKIE]);
    }

    public static function nextFromRequest(): string
    {
        $path = request()->path();
        $qs = request()->getQueryString();

        return $path.($qs !== null && $qs !== '' ? '?'.$qs : '');
    }

    public static function safeRedirectTarget(string $next): string
    {
        if (! preg_match('#^[a-z0-9_/-]+(?:\?[a-zA-Z0-9_=.&%-]*)?$#i', $next)) {
            return auth()->check() ? '/dashboard' : '/';
        }

        return '/'.ltrim($next, '/');
    }
}
