<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

if (current_user_id() !== null) {
    header('Location: ' . url('dashboard.php', $config));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $error = 'Sessão inválida. Atualize a página e tente de novo.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($email === '' || $password === '') {
            $error = 'Preencha email e senha.';
        } else {
            $pdo = db($config);
            $st = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
            $st->execute([$email]);
            $row = $st->fetch();
            if (!$row || !password_verify($password, $row['password_hash'])) {
                $error = 'Email ou senha incorretos.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $row['id'];
                header('Location: ' . url('dashboard.php', $config));
                exit;
            }
        }
    }
}

$title = 'Entrar';
$loggedIn = false;
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Entrar</h1>
        <?php if ($error !== '') : ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>
        <form method="post" class="card" style="max-width: 28rem;" novalidate>
            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" autocomplete="username" required value="<?= h($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
        <p class="muted" style="margin-top: 1rem;"><a href="<?= h(url('register.php', $config)) ?>">Criar conta</a></p>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
