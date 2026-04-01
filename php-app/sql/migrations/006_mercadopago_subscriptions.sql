-- Assinaturas recorrentes (Preapproval Mercado Pago)

CREATE TABLE IF NOT EXISTS `mercadopago_subscriptions` (
  `mp_preapproval_id` VARCHAR(48) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `plan_code` VARCHAR(16) NOT NULL,
  `status` VARCHAR(24) NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mp_preapproval_id`),
  KEY `mercadopago_sub_user` (`user_id`),
  CONSTRAINT `mercadopago_sub_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
