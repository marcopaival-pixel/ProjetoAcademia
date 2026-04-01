<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/theme.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('index.php', $config));
    exit;
}

if (!csrf_verify($_POST['_csrf'] ?? null)) {
    header('Location: ' . url('index.php', $config));
    exit;
}

$t = (string) ($_POST['theme'] ?? '');
$themeVal = $t === 'light' ? 'light' : 'dark';

$next = (string) ($_POST['next'] ?? '');
if ($next === '') {
    $next = current_user_id() !== null ? 'dashboard.php' : 'index.php';
}
$next = projetoacademia_safe_theme_redirect($next);

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? '') === '443');
setcookie(
    PROJETOACADEMIA_THEME_COOKIE,
    $themeVal,
    [
        'expires' => time() + PROJETOACADEMIA_THEME_LIFETIME,
        'path' => '/',
        'secure' => $secure,
        'httponly' => false,
        'samesite' => 'Lax',
    ]
);

header('Location: ' . url($next, $config), true, 303);
exit;
