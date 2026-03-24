-- Migration: 010_create_settings_table
-- FlowDesk Agentur Management Tool

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL,
    INDEX `idx_settings_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standard-Einstellungen einfügen
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
