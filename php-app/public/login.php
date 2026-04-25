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
                <div style="position: relative;">
                    <input type="password" name="password" id="password" required placeholder="Sua senha" style="width: 100%; padding-right: 40px;">
                    <button type="button" onclick="togglePass()" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; display: flex; align-items: center; justify-content: center; padding: 5px;" title="Mostrar/Ocultar senha">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Acessar NexShape</button>
        </form>
        <script>
            function togglePass() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('eyeIcon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                }
            }
        </script>
        <p class="muted" style="margin-top: 1rem;"><a href="<?= h(url('register.php', $config)) ?>">Criar conta</a></p>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
