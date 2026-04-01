<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_login($config);

$status = (string) ($_GET['collection_status'] ?? '');
$paymentId = (string) ($_GET['payment_id'] ?? $_GET['preference_id'] ?? '');

$title = 'Pagamento';
$loggedIn = true;
$navCurrent = 'plano';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Pagamento Mercado Pago</h1>

        <?php if ($status === 'approved') : ?>
            <div class="alert alert-success">
                Pagamento <strong>aprovado</strong>. Seu Premium costuma ser ativado em instantes — atualize a página <a href="<?= h(url('plano.php', $config)) ?>">Meu Plano</a>.
            </div>
        <?php elseif ($status === 'pending') : ?>
            <div class="alert alert-success">
                Pagamento <strong>pendente</strong> (ex.: boleto ou revisão). Quando o Mercado Pago confirmar, o Premium será ativado automaticamente. Acompanhe em <a href="<?= h(url('plano.php', $config)) ?>">Meu Plano</a>.
            </div>
        <?php elseif ($status === 'failure') : ?>
            <div class="alert alert-error">
                O pagamento não foi concluído. Você pode tentar de novo em <a href="<?= h(url('plano.php', $config)) ?>">Meu Plano</a>.
            </div>
        <?php else : ?>
            <p class="lead">Retorno do checkout.</p>
            <?php if ($paymentId !== '') : ?>
                <p class="muted">Referência: <?= h($paymentId) ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <p style="margin-top:1rem;">
            <a class="btn btn-primary" href="<?= h(url('plano.php', $config)) ?>">Meu Plano</a>
            <a class="btn btn-ghost" href="<?= h(url('dashboard.php', $config)) ?>">Hoje</a>
        </p>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
