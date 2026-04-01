-- Idempotência: um pagamento MP só pode creditar Premium uma vez

CREATE TABLE IF NOT EXISTS `mercadopago_payment_credits` (
  `mp_payment_id` BIGINT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `plan_code` VARCHAR(16) NOT NULL,
  `transaction_amount` DECIMAL(12,2) NOT NULL,
  `currency_id` VARCHAR(8) NOT NULL DEFAULT 'BRL',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mp_payment_id`),
  KEY `mercadopago_credits_user` (`user_id`),
  CONSTRAINT `mercadopago_credits_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
