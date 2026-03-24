-- Migration: 002_create_contacts_table
-- FlowDesk Agentur Management Tool

CREATE TABLE IF NOT EXISTS `contacts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `type` ENUM('kunde', 'firma') NOT NULL DEFAULT 'kunde',
    `company_name` VARCHAR(255) NULL,
    `first_name` VARCHAR(255) NULL,
    `last_name` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(50) NULL,
    `address` VARCHAR(255) NULL,
    `plz` VARCHAR(10) NULL,
    `city` VARCHAR(100) NULL,
    `country` VARCHAR(2) NOT NULL DEFAULT 'CH',
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    INDEX `idx_contacts_type` (`type`),
    INDEX `idx_contacts_company` (`company_name`),
    INDEX `idx_contacts_name` (`last_name`, `first_name`),
    INDEX `idx_contacts_email` (`email`),
    INDEX `idx_contacts_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
