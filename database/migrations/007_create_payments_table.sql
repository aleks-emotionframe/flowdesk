-- Migration: 007_create_payments_table
-- FlowDesk Agentur Management Tool

CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_date` DATE NOT NULL,
    `method` ENUM('bank', 'bar', 'kreditkarte') NOT NULL DEFAULT 'bank',
    `reference` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `imported_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_payments_invoice` (`invoice_id`),
    INDEX `idx_payments_date` (`payment_date`),
    CONSTRAINT `fk_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
