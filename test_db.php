<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=projetoacademia;charset=utf8mb4', 'root', '');
    $r = $pdo->query('SELECT COUNT(*) FROM users');
    echo 'Conectado! Usuários: ' . $r->fetchColumn() . "\n";
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . "\n";
}
