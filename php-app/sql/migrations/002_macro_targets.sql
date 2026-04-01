-- Metas diárias de macronutrientes (opcional). Execute se o banco já existia sem estas colunas.

USE `projetoacademia`;

ALTER TABLE `user_profiles`
  ADD COLUMN `protein_target_g` DECIMAL(6,2) DEFAULT NULL AFTER `daily_calorie_target`,
  ADD COLUMN `carbs_target_g` DECIMAL(6,2) DEFAULT NULL AFTER `protein_target_g`,
  ADD COLUMN `fat_target_g` DECIMAL(6,2) DEFAULT NULL AFTER `carbs_target_g`;
