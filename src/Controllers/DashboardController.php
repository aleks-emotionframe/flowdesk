<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Middleware\AuthMiddleware;
use PDO;

class DashboardController
{
    public function index(): void
    {
        AuthMiddleware::check();

        $db = Database::getConnection();
        $user = AuthMiddleware::user();

        // Offene Rechnungen (Summe)
        $stmt = $db->query("SELECT COALESCE(SUM(total), 0) as total FROM invoices WHERE status IN ('gesendet', 'offen', 'überfällig')");
        $offeneRechnungen = (float) $stmt->fetch()['total'];

        // Umsatz aktueller Monat
        $stmt = $db->prepare("SELECT COALESCE(SUM(total), 0) as total FROM invoices WHERE status = 'bezahlt' AND MONTH(paid_at) = MONTH(CURRENT_DATE()) AND YEAR(paid_at) = YEAR(CURRENT_DATE())");
        $stmt->execute();
        $umsatzMonat = (float) $stmt->fetch()['total'];

        // Offene Offerten (Anzahl)
        $stmt = $db->query("SELECT COUNT(*) as count FROM quotes WHERE status IN ('entwurf', 'gesendet')");
        $offeneOfferten = (int) $stmt->fetch()['count'];

        // Überfällige Rechnungen (Anzahl)
        $stmt = $db->query("SELECT COUNT(*) as count FROM invoices WHERE status = 'überfällig' OR (status IN ('gesendet', 'offen') AND due_date < CURRENT_DATE())");
        $ueberfaellige = (int) $stmt->fetch()['count'];

        // Letzte Rechnungen
        $stmt = $db->query("SELECT i.*, c.company_name, c.first_name, c.last_name FROM invoices i LEFT JOIN contacts c ON i.contact_id = c.id ORDER BY i.created_at DESC LIMIT 5");
        $letzteRechnungen = $stmt->fetchAll();

        // Letzte Aktivitäten
        $stmt = $db->query("SELECT a.*, u.name as user_name FROM activities a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 10");
        $letzteAktivitaeten = $stmt->fetchAll();

        // Umsatz pro Monat (aktuelles Jahr) für Chart
        $stmt = $db->prepare("SELECT MONTH(paid_at) as monat, COALESCE(SUM(total), 0) as total FROM invoices WHERE status = 'bezahlt' AND YEAR(paid_at) = YEAR(CURRENT_DATE()) GROUP BY MONTH(paid_at) ORDER BY monat");
        $stmt->execute();
        $umsatzChart = $stmt->fetchAll();

        require __DIR__ . '/../Views/dashboard/index.php';
    }
}
