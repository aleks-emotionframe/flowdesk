<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Invoice
{
    public static function all(string $search = '', string $status = ''): array
    {
        $db = Database::getConnection();
        $where = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[] = '(i.invoice_number LIKE :search OR i.title LIKE :search2 OR c.company_name LIKE :search3 OR c.last_name LIKE :search4)';
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }

        if ($status !== '' && in_array($status, ['entwurf', 'gesendet', 'offen', 'bezahlt', 'überfällig', 'storniert'], true)) {
            $where[] = 'i.status = :status';
            $params['status'] = $status;
        }

        $whereSQL = implode(' AND ', $where);
        $stmt = $db->prepare("SELECT i.*, c.company_name, c.first_name, c.last_name FROM invoices i LEFT JOIN contacts c ON i.contact_id = c.id WHERE {$whereSQL} ORDER BY i.created_at DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT i.*, c.company_name, c.first_name, c.last_name, c.email as contact_email, c.address as contact_address, c.plz as contact_plz, c.city as contact_city FROM invoices i LEFT JOIN contacts c ON i.contact_id = c.id WHERE i.id = :id');
        $stmt->execute(['id' => $id]);
        $invoice = $stmt->fetch();
        return $invoice ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $number = self::nextNumber();

        $stmt = $db->prepare('INSERT INTO invoices (invoice_number, contact_id, quote_id, title, status, due_date, subtotal, mwst_satz, mwst_betrag, total) VALUES (:invoice_number, :contact_id, :quote_id, :title, :status, :due_date, :subtotal, :mwst_satz, :mwst_betrag, :total)');
        $stmt->execute([
            'invoice_number' => $number,
            'contact_id' => $data['contact_id'],
            'quote_id' => $data['quote_id'] ?? null,
            'title' => $data['title'],
            'status' => 'entwurf',
            'due_date' => $data['due_date'] ?: null,
            'subtotal' => $data['subtotal'],
            'mwst_satz' => $data['mwst_satz'],
            'mwst_betrag' => $data['mwst_betrag'],
            'total' => $data['total'],
        ]);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE invoices SET contact_id = :contact_id, title = :title, due_date = :due_date, subtotal = :subtotal, mwst_satz = :mwst_satz, mwst_betrag = :mwst_betrag, total = :total WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'contact_id' => $data['contact_id'],
            'title' => $data['title'],
            'due_date' => $data['due_date'] ?: null,
            'subtotal' => $data['subtotal'],
            'mwst_satz' => $data['mwst_satz'],
            'mwst_betrag' => $data['mwst_betrag'],
            'total' => $data['total'],
        ]);
    }

    public static function updateStatus(int $id, string $status): void
    {
        $db = Database::getConnection();
        $extra = '';
        if ($status === 'gesendet') {
            $extra = ', sent_at = NOW()';
        } elseif ($status === 'bezahlt') {
            $extra = ', paid_at = NOW()';
        }
        $stmt = $db->prepare("UPDATE invoices SET status = :status{$extra} WHERE id = :id");
        $stmt->execute(['id' => $id, 'status' => $status]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM invoices WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function nextNumber(): string
    {
        $db = Database::getConnection();
        $year = date('Y');
        $stmt = $db->prepare("SELECT MAX(CAST(SUBSTRING(invoice_number, 9) AS UNSIGNED)) as max_num FROM invoices WHERE invoice_number LIKE :prefix");
        $stmt->execute(['prefix' => "RE-{$year}-%"]);
        $row = $stmt->fetch();
        $next = ($row['max_num'] ?? 0) + 1;
        return sprintf('RE-%s-%03d', $year, $next);
    }

    public static function getByContact(int $contactId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM invoices WHERE contact_id = :contact_id ORDER BY created_at DESC');
        $stmt->execute(['contact_id' => $contactId]);
        return $stmt->fetchAll();
    }
}
