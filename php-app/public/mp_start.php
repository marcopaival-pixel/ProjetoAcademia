<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/mercadopago.php';

require_login($config);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('plano.php', $config));
    exit;
}

if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $_SESSION['flash_mp_error'] = 'Sessão inválida. Atualize a página e tente de novo.';
    header('Location: ' . url('plano.php', $config));
    exit;
}

$plan = (string) ($_POST['plan'] ?? '');
$checkout = (string) ($_POST['checkout'] ?? 'once');
if (!in_array($plan, ['monthly', 'yearly'], true)) {
    $_SESSION['flash_mp_error'] = 'Plano inválido.';
    header('Location: ' . url('plano.php', $config));
    exit;
}
if (!in_array($checkout, ['once', 'subscribe'], true)) {
    $checkout = 'once';
}

$token = (string) ($config['mercadopago']['access_token'] ?? '');
if ($token === '') {
    $_SESSION['flash_mp_error'] = 'Configure MP_ACCESS_TOKEN no arquivo .env.php (credenciais Mercado Pago).';
    header('Location: ' . url('plano.php', $config));
    exit;
}

$uid = (int) current_user_id();
$pdo = db($config);
$st = $pdo->prepare('SELECT email FROM users WHERE id = ? LIMIT 1');
$st->execute([$uid]);
$row = $st->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    header('Location: ' . url('logout.php', $config));
    exit;
}

if ($checkout === 'subscribe') {
    $go = mp_create_preapproval_subscription($pdo, $config, $token, $uid, (string) $row['email'], $plan);
} else {
    $go = mp_create_checkout_preference($config, $token, $uid, (string) $row['email'], $plan);
}
if (!$go['ok']) {
    $_SESSION['flash_mp_error'] = 'Não foi possível iniciar o checkout: ' . $go['error'];
    header('Location: ' . url('plano.php', $config));
    exit;
}

header('Location: ' . $go['init_point']);
exit;
