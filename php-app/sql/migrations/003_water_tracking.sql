-- Criação da tabela para registro de consumo de água e adição de meta de água no perfil do usuário

CREATE TABLE IF NOT EXISTS `water_entries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `entry_date` DATE NOT NULL,
  `amount_ml` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `water_entries_user_date` (`user_id`, `entry_date`),
  CONSTRAINT `water_entries_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `user_profiles`
ADD COLUMN `water_target_ml` SMALLINT UNSIGNED DEFAULT 2000 AFTER `fat_target_g`;
