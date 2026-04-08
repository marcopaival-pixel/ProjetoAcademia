-- Migração 007: Tabela de logs de erros do sistema
-- Objetivo: Rastreamento automático de falhas e exceções

CREATE TABLE IF NOT EXISTS `logs_erros` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `data_hora` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `tipo_erro` VARCHAR(100) NOT NULL,
    `mensagem` TEXT NOT NULL,
    `arquivo` VARCHAR(255),
    `linha` INT,
    `usuario_id` INT UNSIGNED DEFAULT NULL,
    `ip` VARCHAR(45),
    `url` TEXT,
    `status` VARCHAR(20) DEFAULT 'pendente',
    INDEX (`data_hora`),
    INDEX (`tipo_erro`),
    INDEX (`usuario_id`),
    INDEX (`status`),
    CONSTRAINT `logs_erros_user_fk` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
