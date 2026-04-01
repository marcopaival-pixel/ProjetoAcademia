<?php
declare(strict_types=1);

const PROJETOACADEMIA_THEME_COOKIE = 'projetoacademia_theme';
const PROJETOACADEMIA_THEME_LIFETIME = 365 * 24 * 60 * 60;

function projetoacademia_theme(): string
{
    return (isset($_COOKIE[PROJETOACADEMIA_THEME_COOKIE]) && $_COOKIE[PROJETOACADEMIA_THEME_COOKIE] === 'light')
        ? 'light'
        : 'dark';
}

/** Se falso, a cor vem do sistema até o usuário escolher Escuro/Claro. */
function projetoacademia_theme_is_explicit(): bool
{
    return isset($_COOKIE[PROJETOACADEMIA_THEME_COOKIE]);
}

/**
 * Path relativo seguro para redirecionar após trocar tema (script + query opcional).
 */
function projetoacademia_theme_next_from_request(): string
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $path = parse_url($uri, PHP_URL_PATH) ?? '';
    $query = parse_url($uri, PHP_URL_QUERY);
    $script = basename($path ?: 'index.php');
    if (!preg_match('/^[a-z0-9_-]+\.php$/i', $script)) {
        $script = 'index.php';
    }
    $next = $script;
    if (is_string($query) && $query !== '') {
        $next .= '?' . $query;
    }
    return $next;
}

function projetoacademia_safe_theme_redirect(string $next): string
{
    if (!preg_match('/^[a-z0-9_-]+\.php(?:\?[a-zA-Z0-9_=.&%-]*)?$/i', $next)) {
        return current_user_id() !== null ? 'dashboard.php' : 'index.php';
    }
    return $next;
}
