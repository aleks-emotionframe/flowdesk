-- Migration: 004_create_quote_items_table
-- FlowDesk Agentur Management Tool

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
