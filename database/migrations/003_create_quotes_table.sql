-- Migration: 003_create_quotes_table
-- FlowDesk Agentur Management Tool

CREATE TABLE IF NOT EXISTS `quotes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `quote_number` VARCHAR(20) NOT NULL UNIQUE,
    `contact_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `status` ENUM('entwurf', 'gesendet', 'akzeptiert', 'abgelehnt') NOT NULL DEFAULT 'entwurf',
    `valid_until` DATE NULL,
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `mwst_satz` DECIMAL(4,2) NOT NULL DEFAULT 8.10,
    `mwst_betrag` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `notes` TEXT NULL,
    `sent_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_quotes_contact` (`contact_id`),
    INDEX `idx_quotes_status` (`status`),
    INDEX `idx_quotes_number` (`quote_number`),
    CONSTRAINT `fk_quotes_contact` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
