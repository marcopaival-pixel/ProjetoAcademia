-- ProjetoAcademia — esquema inicial (MySQL 8+)
-- Charset recomendado: utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `projetoacademia`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `projetoacademia`;

DROP TABLE IF EXISTS `food_entries`;
DROP TABLE IF EXISTS `exercise_entries`;
DROP TABLE IF EXISTS `weight_entries`;
DROP TABLE IF EXISTS `user_profiles`;
DROP TABLE IF EXISTS `mercadopago_payment_credits`;
DROP TABLE IF EXISTS `mercadopago_subscriptions`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `is_premium` TINYINT(1) NOT NULL DEFAULT 0,
  `premium_expires_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mercadopago_payment_credits` (
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

CREATE TABLE `mercadopago_subscriptions` (
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

CREATE TABLE `user_profiles` (
  `user_id` INT UNSIGNED NOT NULL,
  `birth_date` DATE DEFAULT NULL,
  `sex` ENUM('M','F','O','') NOT NULL DEFAULT '',
  `height_cm` SMALLINT UNSIGNED DEFAULT NULL,
  `activity_level` ENUM('sedentary','light','moderate','active','very_active') NOT NULL DEFAULT 'moderate',
  `goal` ENUM('lose','gain','maintain') NOT NULL DEFAULT 'maintain',
  `daily_calorie_target` INT UNSIGNED DEFAULT NULL,
  `protein_target_g` DECIMAL(6,2) DEFAULT NULL,
  `carbs_target_g` DECIMAL(6,2) DEFAULT NULL,
  `fat_target_g` DECIMAL(6,2) DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_profiles_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `food_entries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `entry_date` DATE NOT NULL,
  `meal_type` ENUM('breakfast','lunch','dinner','snack','other') NOT NULL DEFAULT 'other',
  `food_name` VARCHAR(200) NOT NULL,
  `calories` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `protein_g` DECIMAL(6,2) NOT NULL DEFAULT 0,
  `carbs_g` DECIMAL(6,2) NOT NULL DEFAULT 0,
  `fat_g` DECIMAL(6,2) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `food_entries_user_date` (`user_id`, `entry_date`),
  CONSTRAINT `food_entries_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exercise_entries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `entry_date` DATE NOT NULL,
  `activity_type` VARCHAR(120) NOT NULL,
  `duration_min` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `calories_burned` SMALLINT UNSIGNED DEFAULT NULL,
  `notes` VARCHAR(500) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `exercise_entries_user_date` (`user_id`, `entry_date`),
  CONSTRAINT `exercise_entries_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `weight_entries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `weighed_at` DATE NOT NULL,
  `weight_kg` DECIMAL(5,2) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `weight_entries_user_day` (`user_id`, `weighed_at`),
  CONSTRAINT `weight_entries_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
