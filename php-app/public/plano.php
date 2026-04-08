<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/mercadopago.php';
require_login($config);

$pdo = db($config);
$isPremium = is_current_user_premium($pdo);

$mpFlash = (string) ($_SESSION['flash_mp_error'] ?? '');
unset($_SESSION['flash_mp_error']);

$mpConfigured = ($config['mercadopago']['access_token'] ?? '') !== ''
    && ($config['app']['public_url'] ?? '') !== '';

$title = 'Meu Plano';
$loggedIn = true;
$navCurrent = 'plano';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Meu Plano</h1>

        <?php if ($mpFlash !== '') : ?>
            <div class="alert alert-error"><?= h($mpFlash) ?></div>
        <?php endif; ?>
        <?php if (!$mpConfigured && !$isPremium) : ?>
            <div class="alert alert-error">
                Para pagar com Mercado Pago, configure <strong>APP_PUBLIC_URL</strong> e <strong>MP_ACCESS_TOKEN</strong> no <code>.env.php</code>
                (veja <code>.env.example.php</code>). Em produção use HTTPS na URL pública.
            </div>
        <?php endif; ?>

        <?php if ($isPremium) : ?>
            <div class="alert alert-success">
                Você está com o plano <strong>Premium</strong> ativo. Obrigado por apoiar o ProjetoAcademia.
            </div>
            <p class="lead">Continue aproveitando metas de macros personalizadas, exportação CSV e todos os benefícios abaixo.</p>
        <?php else : ?>
            <p class="lead">Desbloqueie recursos para quem leva treino e dieta a sério — sem perder a simplicidade do app.</p>
        <?php endif; ?>

        <div class="plan-grid">
            <article class="card plan-card plan-card--free">
                <h2 style="margin-top:0;">Grátis</h2>
                <p class="plan-price muted">R$ 0</p>
                <ul class="plan-features">
                    <li>Diário alimentar e exercícios</li>
                    <li>Meta calórica e macros <strong>automáticos</strong> (repartição padrão a partir das kcal)</li>
                    <li>Peso, água e relatório semanal na tela</li>
                </ul>
            </article>

            <article class="card plan-card plan-card--premium">
                <div class="plan-card__badge"><span aria-hidden="true">👑</span> Premium</div>
                <h2 style="margin-top:0;">Premium</h2>
                <p class="plan-price">Pagamento único (renovar quando quiser) ou assinatura recorrente no Mercado Pago.</p>
                <ul class="plan-features">
                    <li><strong>Metas de macros personalizadas</strong> (proteína, carbo, gordura em gramas)</li>
                    <li><strong>Exportar CSV</strong> — alimentação, exercícios e peso para Excel ou nutricionista</li>
                    <li>Experiência pensada para evoluir (sem anúncios, conforme configurarmos o app)</li>
                </ul>

                <?php if (!$isPremium) : ?>
                    <div class="plan-cta">
                        <h3 class="plan-subtitle">Pagamento único</h3>
                        <p class="plan-price-line"><strong>Mensal</strong> — R$ 19,90 (acrescenta <strong>1 mês</strong> ao Premium).</p>
                        <form method="post" action="<?= h(url('mp_start.php', $config)) ?>" style="margin:0;">
                            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="plan" value="monthly">
                            <input type="hidden" name="checkout" value="once">
                            <button type="submit" class="btn btn-primary"<?= $mpConfigured ? '' : ' disabled' ?>>
                                Pagar avulso — mensal
                            </button>
                        </form>
                        <p class="plan-price-line" style="margin-top:1rem;"><strong>Anual</strong> — R$ 149,90 (acrescenta <strong>1 ano</strong>).</p>
                        <form method="post" action="<?= h(url('mp_start.php', $config)) ?>" style="margin:0;">
                            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="plan" value="yearly">
                            <input type="hidden" name="checkout" value="once">
                            <button type="submit" class="btn btn-primary"<?= $mpConfigured ? '' : ' disabled' ?>>
                                Pagar avulso — anual
                            </button>
                        </form>

                        <h3 class="plan-subtitle" style="margin-top:1.5rem;">Assinatura recorrente</h3>
                        <p class="muted" style="margin:0 0 0.75rem; font-size:0.875rem;">
                            Cobrança automática no Mercado Pago (cartão em arquivo). Cada cobrança aprovada estende seu Premium pelo período do plano. Cancele quando quiser no app ou site do MP.
                        </p>
                        <p class="plan-price-line"><strong>Mensal</strong> — R$ 19,90 / mês</p>
                        <form method="post" action="<?= h(url('mp_start.php', $config)) ?>" style="margin:0;">
                            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="plan" value="monthly">
                            <input type="hidden" name="checkout" value="subscribe">
                            <button type="submit" class="btn btn-ghost"<?= $mpConfigured ? '' : ' disabled' ?>>
                                Assinar mensal (recorrente)
                            </button>
                        </form>
                        <p class="plan-price-line" style="margin-top:1rem;"><strong>Anual</strong> — R$ 149,90 / ano</p>
                        <form method="post" action="<?= h(url('mp_start.php', $config)) ?>" style="margin:0;">
                            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="plan" value="yearly">
                            <input type="hidden" name="checkout" value="subscribe">
                            <button type="submit" class="btn btn-ghost"<?= $mpConfigured ? '' : ' disabled' ?>>
                                Assinar anual (recorrente)
                            </button>
                        </form>

                        <p class="muted plan-hint" style="margin:1rem 0 0; font-size:0.8125rem;">
                            Checkout seguro no Mercado Pago. O <strong>webhook</strong> confirma pagamentos e alterações de assinatura.
                            <?php if ($mpConfigured) : ?>
                                URL: <code><?= h(mp_absolute_url('mp_webhook.php', $config)) ?></code>
                            <?php else : ?>
                                Com <code>APP_PUBLIC_URL</code> preenchido, a URL aparece aqui.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </article>
        </div>

        <p class="muted" style="margin-top:1.5rem; font-size:0.875rem;">
            Cancelamento de assinaturas recorrentes é feito no Mercado Pago; o app revoga o Premium quando receber o evento de cancelamento (pode levar alguns minutos).
        </p>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
