-- Migration: 011_seed_admin_user
-- FlowDesk Agentur Management Tool
-- Erstellt den initialen Admin-Benutzer
-- Passwort: admin123 (MUSS nach erstem Login geändert werden!)
-- Hash generiert mit PHP password_hash('admin123', PASSWORD_DEFAULT)

INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
    ('Administrator', 'admin@flowdesk.ch', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE `name` = `name`;
