-- Adição de campos para gerenciamento de assinatura Premium

ALTER TABLE `users`
ADD COLUMN `is_premium` TINYINT(1) NOT NULL DEFAULT 0 AFTER `name`,
ADD COLUMN `premium_expires_at` DATETIME DEFAULT NULL AFTER `is_premium`;
