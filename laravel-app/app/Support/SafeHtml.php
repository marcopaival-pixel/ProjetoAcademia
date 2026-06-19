<?php

namespace App\Support;

use Illuminate\Support\Str;

class SafeHtml
{
    /** Tags permitidas em conteúdo editorial (KB, treinamento). */
    private const ALLOWED_TAGS = '<p><br><strong><em><b><i><ul><ol><li><h1><h2><h3><h4><h5><h6><a><blockquote><code><pre><span><div><table><thead><tbody><tr><th><td>';

    public static function clean(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $cleaned = strip_tags($html, self::ALLOWED_TAGS);

        return self::sanitizeLinks($cleaned);
    }

    /**
     * Escapa texto e preserva quebras de linha (conteúdo plain-text).
     */
    public static function markdown(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        return self::clean(Str::markdown($text));
    }

    public static function nl2brEscaped(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        return nl2br(e($text), false);
    }

    private static function sanitizeLinks(string $html): string
    {
        return (string) preg_replace_callback(
            '/<a\s+([^>]*href\s*=\s*["\'])([^"\']*)(["\'][^>]*)>/i',
            function (array $matches) {
                $href = $matches[2];
                if (preg_match('/^\s*javascript:/i', $href) || preg_match('/^\s*data:/i', $href)) {
                    return '<a href="#" rel="noopener noreferrer">';
                }

                return '<a '.$matches[1].$href.$matches[3].' rel="noopener noreferrer">';
            },
            $html
        );
    }
}
