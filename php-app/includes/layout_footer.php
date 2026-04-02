    </main>
    <footer class="site-footer shell">
        <div class="footer-row">
            <p class="footer-tagline">ProjetoAcademia — acompanhamento alimentar e exercícios.</p>
            <div class="theme-switcher" role="group" aria-label="Tema da interface">
                <span class="muted theme-switcher-label">Aparência<?= !projetoacademia_theme_is_explicit() ? ' <span class="theme-auto-hint">(sistema)</span>' : '' ?></span>
                <?php $themeExplicit = projetoacademia_theme_is_explicit(); ?>
                <form method="post" action="<?= h(url('set_theme.php', $config)) ?>" class="theme-switcher-form">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="next" value="<?= h(projetoacademia_theme_next_from_request()) ?>">
                    <input type="hidden" name="theme" value="dark">
                    <button type="submit" class="btn-theme<?= $themeExplicit && $projetoAcademiaTheme === 'dark' ? ' is-active' : '' ?>">Escuro</button>
                </form>
                <form method="post" action="<?= h(url('set_theme.php', $config)) ?>" class="theme-switcher-form">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="next" value="<?= h(projetoacademia_theme_next_from_request()) ?>">
                    <input type="hidden" name="theme" value="light">
                    <button type="submit" class="btn-theme<?= $themeExplicit && $projetoAcademiaTheme === 'light' ? ' is-active' : '' ?>">Claro</button>
                </form>
            </div>
        </div>
    </footer>
    <script src="<?= h(url('js/app.js', $config)) ?>" defer></script>
</body>
</html>
