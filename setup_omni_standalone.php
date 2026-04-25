<?php
/**
 * Omnichannel Standalone Setup
 * Este script configura o banco de dados diretamente, ignorando rotas do Laravel.
 */

$host = '127.0.0.1';
$dbname = 'projetoacademia';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "--- Iniciando Setup Omnichannel ---\n";

    // 1. Criar tabelas se não existirem (Simplificadas para o setup inicial)
    $sql = "
    CREATE TABLE IF NOT EXISTS omni_companies (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    );

    CREATE TABLE IF NOT EXISTS omni_channels (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        company_id BIGINT UNSIGNED NOT NULL,
        type ENUM('whatsapp', 'widget', 'api') DEFAULT 'widget',
        name VARCHAR(255) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    );

    CREATE TABLE IF NOT EXISTS omni_agents (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        company_id BIGINT UNSIGNED NOT NULL,
        status ENUM('online', 'offline', 'busy') DEFAULT 'offline',
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    );

    CREATE TABLE IF NOT EXISTS omni_conversations (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        company_id BIGINT UNSIGNED NOT NULL,
        channel_id BIGINT UNSIGNED NOT NULL,
        customer_external_id VARCHAR(255) NOT NULL,
        customer_name VARCHAR(255),
        status ENUM('pending', 'open', 'closed', 'bot') DEFAULT 'bot',
        last_message_at TIMESTAMP NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    );

    CREATE TABLE IF NOT EXISTS omni_messages (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        conversation_id BIGINT UNSIGNED NOT NULL,
        sender_type ENUM('customer', 'agent', 'bot', 'system') NOT NULL,
        sender_id INT UNSIGNED NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    );
    ";

    $pdo->exec("USE $dbname;");
    $pdo->exec($sql);
    echo "✅ Tabelas criadas/verificadas.\n";

    // 2. Criar dados de teste
    $pdo->exec("INSERT IGNORE INTO omni_companies (id, name, slug, created_at, updated_at) VALUES (1, 'Academia Central', 'academia-central', NOW(), NOW())");
    $pdo->exec("INSERT IGNORE INTO omni_channels (id, company_id, type, name, created_at, updated_at) VALUES (1, 1, 'widget', 'Chat Principal', NOW(), NOW())");
    
    // Pegar o primeiro admin do sistema para ser o agente
    $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
    $userId = $stmt->fetchColumn();
    if($userId) {
        $pdo->exec("INSERT IGNORE INTO omni_agents (user_id, company_id, status, created_at, updated_at) VALUES ($userId, 1, 'online', NOW(), NOW())");
        echo "✅ Usuário ID $userId configurado como Agente.\n";
    }

    echo "\n🎉 Setup concluído! Agora você pode acessar o painel administrativo.\n";

} catch (Exception $e) {
    die("❌ Erro no Setup: " . $e->getMessage() . "\n");
}
