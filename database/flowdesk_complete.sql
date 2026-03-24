-- =====================================================
-- FlowDesk - Komplettes Datenbank-Setup
-- Einfach in phpMyAdmin einfügen und ausführen
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `avatar` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Contacts
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

-- 3. Quotes (Offerten)
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

-- 4. Quote Items
CREATE TABLE IF NOT EXISTS `quote_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `quote_id` INT UNSIGNED NOT NULL,
    `position` INT UNSIGNED NOT NULL,
    `description` TEXT NOT NULL,
    `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    `unit` ENUM('Stk', 'Std', 'Pauschal') NOT NULL DEFAULT 'Stk',
    `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    INDEX `idx_quote_items_quote` (`quote_id`),
    INDEX `idx_quote_items_position` (`quote_id`, `position`),
    CONSTRAINT `fk_quote_items_quote` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Invoices (Rechnungen)
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

-- 6. Invoice Items
CREATE TABLE IF NOT EXISTS `invoice_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT UNSIGNED NOT NULL,
    `position` INT UNSIGNED NOT NULL,
    `description` TEXT NOT NULL,
    `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    `unit` ENUM('Stk', 'Std', 'Pauschal') NOT NULL DEFAULT 'Stk',
    `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    INDEX `idx_invoice_items_invoice` (`invoice_id`),
    INDEX `idx_invoice_items_position` (`invoice_id`, `position`),
    CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Payments
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

-- 8. Recurring Invoices
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

-- 9. Activities
CREATE TABLE IF NOT EXISTS `activities` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `type` VARCHAR(50) NOT NULL,
    `reference_type` VARCHAR(50) NULL,
    `reference_id` INT UNSIGNED NULL,
    `description` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_activities_user` (`user_id`),
    INDEX `idx_activities_type` (`type`),
    INDEX `idx_activities_reference` (`reference_type`, `reference_id`),
    INDEX `idx_activities_created` (`created_at`),
    CONSTRAINT `fk_activities_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Settings
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL,
    INDEX `idx_settings_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- Stammdaten einfügen
-- =====================================================

-- Admin-User (Passwort: admin123 - NACH LOGIN ÄNDERN!)
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
    ('Administrator', 'admin@flowdesk.ch', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE `name` = `name`;

-- Standard-Einstellungen
INSERT INTO `settings` (`key`, `value`) VALUES
    ('company_name', 'Deine Agentur GmbH'),
    ('company_address', 'Musterstrasse 1'),
    ('company_plz', '8000'),
    ('company_city', 'Zürich'),
    ('company_country', 'CH'),
    ('company_phone', '+41 44 000 00 00'),
    ('company_email', 'info@deineagentur.ch'),
    ('company_uid', 'CHE-123.456.789'),
    ('company_iban', 'CH00 0000 0000 0000 0000 0'),
    ('company_bank', 'Raiffeisen'),
    ('company_mwst_nr', 'CHE-123.456.789 MWST'),
    ('company_mwst_satz', '8.1'),
    ('company_logo', NULL)
ON DUPLICATE KEY UPDATE `key` = `key`;
