-- Migration: 008_create_recurring_invoices_table
-- FlowDesk Agentur Management Tool

CREATE TABLE IF NOT EXISTS `recurring_invoices` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contact_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `interval_type` ENUM('monatlich', 'quartalsweise', 'jährlich') NOT NULL DEFAULT 'monatlich',
    `next_date` DATE NOT NULL,
    `items_json` JSON NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_recurring_contact` (`contact_id`),
    INDEX `idx_recurring_active` (`active`),
    INDEX `idx_recurring_next_date` (`next_date`),
    CONSTRAINT `fk_recurring_contact` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
