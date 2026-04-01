<?php
declare(strict_types=1);
/** @var string $title */
/** @var array<string, mixed> $config */
/** @var bool $loggedIn */
/** @var string $navCurrent slug opcional: dashboard|diary|exercise|weight|report|export|plano|profile */
$navCurrent = $navCurrent ?? '';
$bp = base_path($config);
require_once __DIR__ . '/theme.php';
$projetoAcademiaTheme = projetoacademia_theme();
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?= h($projetoAcademiaTheme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> — ProjetoAcademia</title>
    <script>
        (function () {
            var n = <?= json_encode(PROJETOACADEMIA_THEME_COOKIE, JSON_UNESCAPED_SLASHES) ?>;
            if (document.cookie.indexOf(n + "=") !== -1) {
                return;
            }
            try {
                if (window.matchMedia("(prefers-color-scheme: light)").matches) {
                    document.documentElement.setAttribute("data-theme", "light");
                }
            } catch (e) {}
        })();
    </script>
    <link rel="stylesheet" href="<?= h($bp) ?>/css/app.css">
</head>
<body>
    <a class="skip-link" href="#main">Ir para o conteúdo</a>
    <header class="site-header">
        <div class="shell header-inner">
            <a class="logo" href="<?= h(url($loggedIn ? 'dashboard.php' : 'index.php', $config)) ?>">ProjetoAcademia</a>
            <?php if ($loggedIn) : ?>
                <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" id="nav-toggle">Menu</button>
                <nav class="site-nav" id="site-nav" aria-label="Principal">
                    <a href="<?= h(url('dashboard.php', $config)) ?>"<?= $navCurrent === 'dashboard' ? ' aria-current="page"' : '' ?>>Hoje</a>
                    <a href="<?= h(url('diary.php', $config)) ?>"<?= $navCurrent === 'diary' ? ' aria-current="page"' : '' ?>>Alimentação</a>
                    <a href="<?= h(url('exercise.php', $config)) ?>"<?= $navCurrent === 'exercise' ? ' aria-current="page"' : '' ?>>Exercícios</a>
                    <a href="<?= h(url('weight.php', $config)) ?>"<?= $navCurrent === 'weight' ? ' aria-current="page"' : '' ?>>Peso</a>
                    <a href="<?= h(url('report.php', $config)) ?>"<?= $navCurrent === 'report' ? ' aria-current="page"' : '' ?>>Relatório</a>
                    <a href="<?= h(url('export.php', $config)) ?>"<?= $navCurrent === 'export' ? ' aria-current="page"' : '' ?>>Exportar</a>
                    <a href="<?= h(url('plano.php', $config)) ?>"<?= $navCurrent === 'plano' ? ' aria-current="page"' : '' ?>>Meu Plano</a>
                    <a href="<?= h(url('profile.php', $config)) ?>"<?= $navCurrent === 'profile' ? ' aria-current="page"' : '' ?>>Perfil</a>
                    <a href="<?= h(url('logout.php', $config)) ?>">Sair</a>
                </nav>
            <?php endif; ?>
        </div>
    </header>
    <main id="main" class="shell main-content">
