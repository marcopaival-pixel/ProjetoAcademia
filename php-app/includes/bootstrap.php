<?php
declare(strict_types=1);

$config = require __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($config['app']['session_name']);
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * @param array<string, mixed> $config
 */
function base_path(array $config): string
{
    return $config['app']['base_path'];
}

/**
 * @param array<string, mixed> $config
 */
function url(string $path, array $config): string
{
    $p = '/' . ltrim($path, '/');
    $b = base_path($config);
    return ($b !== '' ? $b : '') . $p;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_verify(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['_csrf'])
        && hash_equals($_SESSION['_csrf'], $token);
}

function require_login(array $config): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . url('login.php', $config));
        exit;
    }
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function is_current_user_premium(PDO $pdo): bool
{
    $uid = current_user_id();
    if (!$uid) {
        return false;
    }
    $stmt = $pdo->prepare('SELECT is_premium, premium_expires_at FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$uid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return false;
    }
    if (!(bool) $row['is_premium']) {
        return false;
    }
    $expRaw = $row['premium_expires_at'];
    if ($expRaw !== null && $expRaw !== '') {
        try {
            $expAt = new DateTimeImmutable((string) $expRaw);
            if ($expAt < new DateTimeImmutable('now')) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    return true;
}
