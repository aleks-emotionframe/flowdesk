-- Migration: 005_create_invoices_table
-- FlowDesk Agentur Management Tool

CREATE TABLE IF NOT EXISTS `invoices` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(20) NOT NULL UNIQUE,
    `contact_id` INT UNSIGNED NOT NULL,
    `quote_id` INT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `status` ENUM('entwurf', 'gesendet', 'offen', 'bezahlt', 'überfällig', 'storniert') NOT NULL DEFAULT 'entwurf',
    `due_date` DATE NULL,
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `mwst_satz` DECIMAL(4,2) NOT NULL DEFAULT 8.10,
    `mwst_betrag` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `paid_at` DATETIME NULL,
    `sent_at` DATETIME NULL,
    `reminder_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_invoices_contact` (`contact_id`),
    INDEX `idx_invoices_quote` (`quote_id`),
    INDEX `idx_invoices_status` (`status`),
    INDEX `idx_invoices_number` (`invoice_number`),
    INDEX `idx_invoices_due_date` (`due_date`),
    CONSTRAINT `fk_invoices_contact` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_invoices_quote` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
