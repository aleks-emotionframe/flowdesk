<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Quote
{
    public static function all(string $search = '', string $status = ''): array
    {
        $db = Database::getConnection();
        $where = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[] = '(q.quote_number LIKE :search OR q.title LIKE :search2 OR c.company_name LIKE :search3 OR c.last_name LIKE :search4)';
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }

        if ($status !== '' && in_array($status, ['entwurf', 'gesendet', 'akzeptiert', 'abgelehnt'], true)) {
            $where[] = 'q.status = :status';
            $params['status'] = $status;
        }

        $whereSQL = implode(' AND ', $where);
        $stmt = $db->prepare("SELECT q.*, c.company_name, c.first_name, c.last_name FROM quotes q LEFT JOIN contacts c ON q.contact_id = c.id WHERE {$whereSQL} ORDER BY q.created_at DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT q.*, c.company_name, c.first_name, c.last_name, c.email as contact_email, c.address as contact_address, c.plz as contact_plz, c.city as contact_city FROM quotes q LEFT JOIN contacts c ON q.contact_id = c.id WHERE q.id = :id');
        $stmt->execute(['id' => $id]);
        $quote = $stmt->fetch();
        return $quote ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $number = self::nextNumber();

        $stmt = $db->prepare('INSERT INTO quotes (quote_number, contact_id, title, status, valid_until, subtotal, mwst_satz, mwst_betrag, total, notes) VALUES (:quote_number, :contact_id, :title, :status, :valid_until, :subtotal, :mwst_satz, :mwst_betrag, :total, :notes)');
        $stmt->execute([
            'quote_number' => $number,
            'contact_id' => $data['contact_id'],
            'title' => $data['title'],
            'status' => 'entwurf',
            'valid_until' => $data['valid_until'] ?: null,
            'subtotal' => $data['subtotal'],
            'mwst_satz' => $data['mwst_satz'],
            'mwst_betrag' => $data['mwst_betrag'],
            'total' => $data['total'],
            'notes' => $data['notes'] ?: null,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE quotes SET contact_id = :contact_id, title = :title, valid_until = :valid_until, subtotal = :subtotal, mwst_satz = :mwst_satz, mwst_betrag = :mwst_betrag, total = :total, notes = :notes WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'contact_id' => $data['contact_id'],
            'title' => $data['title'],
            'valid_until' => $data['valid_until'] ?: null,
            'subtotal' => $data['subtotal'],
            'mwst_satz' => $data['mwst_satz'],
            'mwst_betrag' => $data['mwst_betrag'],
            'total' => $data['total'],
            'notes' => $data['notes'] ?: null,
        ]);
    }

    public static function updateStatus(int $id, string $status): void
    {
        $db = Database::getConnection();
        $extra = '';
        if ($status === 'gesendet') {
            $extra = ', sent_at = NOW()';
        }
        $stmt = $db->prepare("UPDATE quotes SET status = :status{$extra} WHERE id = :id");
        $stmt->execute(['id' => $id, 'status' => $status]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM quotes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function nextNumber(): string
    {
        $db = Database::getConnection();
        $year = date('Y');
        $stmt = $db->prepare("SELECT MAX(CAST(SUBSTRING(quote_number, 9) AS UNSIGNED)) as max_num FROM quotes WHERE quote_number LIKE :prefix");
        $stmt->execute(['prefix' => "OF-{$year}-%"]);
        $row = $stmt->fetch();
        $next = ($row['max_num'] ?? 0) + 1;
        return sprintf('OF-%s-%03d', $year, $next);
    }

    public static function getByContact(int $contactId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM quotes WHERE contact_id = :contact_id ORDER BY created_at DESC');
        $stmt->execute(['contact_id' => $contactId]);
        return $stmt->fetchAll();
    }
}
