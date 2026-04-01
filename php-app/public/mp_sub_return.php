<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_login($config);

$status = (string) ($_GET['preapproval_status'] ?? $_GET['status'] ?? '');
$preId = (string) ($_GET['preapproval_id'] ?? $_GET['collection_id'] ?? '');

$title = 'Assinatura';
$loggedIn = true;
$navCurrent = 'plano';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Assinatura Mercado Pago</h1>

        <?php if ($status === 'authorized' || $status === 'approved') : ?>
            <div class="alert alert-success">
                Assinatura <strong>autorizada</strong>. Sua primeira cobrança pode levar alguns instantes; o Premium é ativado quando o pagamento for confirmado. Consulte <a href="<?= h(url('plano.php', $config)) ?>">Meu Plano</a>.
            </div>
        <?php elseif ($status === 'pending' || $status === '') : ?>
            <div class="alert alert-success">
                Processo em andamento no Mercado Pago. Quando a assinatura for autorizada e o pagamento aprovado, o Premium será liberado automaticamente.
            </div>
        <?php else : ?>
            <div class="alert alert-error">
                Não foi possível concluir a assinatura. Tente novamente em <a href="<?= h(url('plano.php', $config)) ?>">Meu Plano</a>.
            </div>
        <?php endif; ?>

        <?php if ($preId !== '') : ?>
            <p class="muted" style="font-size:0.875rem;">Referência assinatura: <code><?= h($preId) ?></code></p>
        <?php endif; ?>

        <p style="margin-top:1rem;">
            <a class="btn btn-primary" href="<?= h(url('plano.php', $config)) ?>">Meu Plano</a>
            <a class="btn btn-ghost" href="<?= h(url('dashboard.php', $config)) ?>">Hoje</a>
        </p>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
