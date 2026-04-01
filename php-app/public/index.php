<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

if (current_user_id() !== null) {
    header('Location: ' . url('dashboard.php', $config));
    exit;
}

$title = 'Início';
$loggedIn = false;
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>ProjetoAcademia</h1>
        <p class="lead">Acompanhe alimentação, exercícios e peso em um só lugar — layout pensado para celular e desktop.</p>
        <div class="actions-inline">
            <a class="btn btn-primary" href="<?= h(url('login.php', $config)) ?>">Entrar</a>
            <a class="btn btn-ghost" href="<?= h(url('register.php', $config)) ?>">Criar conta</a>
        </div>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
