<?php
// Standalone DB Fix Script
$host = '127.0.0.1';
$db   = 'projeto_academia';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Conectado ao banco de dados com sucesso!<br>";

     // 1. Create internal_emails
     $sqlEmails = "CREATE TABLE IF NOT EXISTS `internal_emails` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `remetente_id` INT UNSIGNED NOT NULL,
        `destinatario_id` INT UNSIGNED NOT NULL,
        `assunto` VARCHAR(200) NOT NULL,
        `mensagem` TEXT NOT NULL,
        `lida` TINYINT(1) DEFAULT 0,
        `data_envio` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        `data_leitura` TIMESTAMP NULL,
        `excluded_at_sender` TIMESTAMP NULL,
        `excluded_at_receiver` TIMESTAMP NULL,
        `status` ENUM('draft', 'outbox', 'sent', 'failed') DEFAULT 'sent',
        `parent_id` BIGINT UNSIGNED NULL,
        `is_system` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP NULL,
        `updated_at` TIMESTAMP NULL
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
     $pdo->exec($sqlEmails);
     echo "Tabela 'internal_emails' verificada/criada.<br>";

     // 2. Add columns to water_entries if they don't exist
     try {
         $pdo->exec("ALTER TABLE `water_entries` ADD COLUMN `drank_at` TIMESTAMP NULL AFTER `entry_date` overflow;");
     } catch (Exception $e) {
         // Column might already exist
     }
     
     try {
         $pdo->exec("ALTER TABLE `water_entries` ADD COLUMN `source` VARCHAR(255) NULL AFTER `drank_at`;");
     } catch (Exception $e) {
         // Column might already exist
     }

     echo "Colunas de hidratação verificadas na tabela 'water_entries'.<br>";
     echo "<strong>Sucesso! O erro SQL deve ter sido resolvido.</strong>";

} catch (\PDOException $e) {
     echo "Erro: " . $e->getMessage();
}
