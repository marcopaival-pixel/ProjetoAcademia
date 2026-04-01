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
        /*
         * Nota de sanitização UTF-8: Normalizer::normalize exige ext-intl em alguns ambientes;
         * manter trim simples evita dependência extra no MVP.
         */
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $error = 'Preencha nome, email e senha.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido.';
        } elseif (strlen($password) < 8) {
            $error = 'Use pelo menos 8 caracteres na senha.';
        } else {
            $pdo = db($config);
            $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = 'Este email já está cadastrado.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->beginTransaction();
                try {
                    $ins = $pdo->prepare(
                        'INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)'
                    );
                    $ins->execute([$email, $hash, $name]);
                    $uid = (int) $pdo->lastInsertId();
                    $pdo->prepare(
                        'INSERT INTO user_profiles (user_id) VALUES (?)'
                    )->execute([$uid]);
                    $pdo->commit();
                } catch (Throwable $e) {
                    $pdo->rollBack();
                    $error = 'Não foi possível criar a conta. Tente novamente.';
                }
                if ($error === '') {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $uid;
                    header('Location: ' . url('dashboard.php', $config));
                    exit;
                }
            }
        }
    }
}

$title = 'Criar conta';
$loggedIn = false;
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Criar conta</h1>
        <?php if ($error !== '') : ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>
        <form method="post" class="card" style="max-width: 28rem;" novalidate>
            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
            <div class="form-group">
                <label for="name">Nome</label>
                <input id="name" name="name" type="text" required autocomplete="name" value="<?= h($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required autocomplete="email" value="<?= h($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Senha (mín. 8 caracteres)</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
        <p class="muted" style="margin-top: 1rem;"><a href="<?= h(url('login.php', $config)) ?>">Já tenho conta</a></p>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
