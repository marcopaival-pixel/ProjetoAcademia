<?php
/**
 * Script temporário de setup — apague após usar!
 * Acesse via browser: http://localhost/ProjetoAcademia/laravel-app/public/setup_body_images.php
 */
$src = 'C:/Users/paiva/.gemini/antigravity/brain/5d69c1a7-3818-40c0-9e37-4d2d45b59bf0/';
$dst = __DIR__ . '/images/body/';

if (!is_dir($dst)) {
    mkdir($dst, 0755, true);
}

$files = [
    'body_female_front_1775673375488.png' => 'female_front.png',
    'body_female_back_1775673389987.png'  => 'female_back.png',
    'body_male_front_1775673405350.png'   => 'male_front.png',
    'body_male_back_1775673421690.png'    => 'male_back.png',
];

echo '<html><head><meta charset="utf-8"><title>Setup Body Images</title></head>';
echo '<body style="font-family:monospace;background:#0b0e14;color:#e2e8f0;padding:2rem;">';
echo '<h2 style="color:#60a5fa;">🚀 Setup: Body Images</h2>';

foreach ($files as $from => $to) {
    $source = $src . $from;
    $target = $dst . $to;
    if (file_exists($source)) {
        if (copy($source, $target)) {
            echo "<p style='color:#34d399;'>✅ Copiado: <b>$to</b> (" . round(filesize($target)/1024) . " KB)</p>";
        } else {
            echo "<p style='color:#f87171;'>❌ Falha ao copiar: <b>$to</b></p>";
        }
    } else {
        echo "<p style='color:#fbbf24;'>⚠️ Ficheiro fonte não encontrado: <b>$from</b></p>";
    }
}

echo '<hr style="border-color:#1e293b;margin:1.5rem 0;">';
echo '<p style="color:#94a3b8;">✔ Concluído. <b>Apague este ficheiro</b> após verificar a página.</p>';
echo '<p><a href="/ProjetoAcademia/laravel-app/public/progression/plans/target-selection" style="color:#60a5fa;">→ Ir para a página</a></p>';
echo '</body></html>';
