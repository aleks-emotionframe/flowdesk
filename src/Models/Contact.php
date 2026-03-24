<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Contact
{
    public static function all(string $search = '', string $type = ''): array
    {
        $db = Database::getConnection();
        $where = ['deleted_at IS NULL'];
        $params = [];

        if ($search !== '') {
            $where[] = '(company_name LIKE :search OR first_name LIKE :search2 OR last_name LIKE :search3 OR email LIKE :search4)';
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }

        if ($type !== '' && in_array($type, ['kunde', 'firma'], true)) {
            $where[] = 'type = :type';
            $params['type'] = $type;
        }

        $whereSQL = implode(' AND ', $where);
        $stmt = $db->prepare("SELECT * FROM contacts WHERE {$whereSQL} ORDER BY COALESCE(company_name, last_name, first_name) ASC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM contacts WHERE id = :id AND deleted_at IS NULL');
        $stmt->execute(['id' => $id]);
        $contact = $stmt->fetch();
        return $contact ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('INSERT INTO contacts (type, company_name, first_name, last_name, email, phone, address, plz, city, country, notes) VALUES (:type, :company_name, :first_name, :last_name, :email, :phone, :address, :plz, :city, :country, :notes)');
        $stmt->execute([
            'type' => $data['type'],
            'company_name' => $data['company_name'] ?: null,
            'first_name' => $data['first_name'] ?: null,
            'last_name' => $data['last_name'] ?: null,
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,
            'plz' => $data['plz'] ?: null,
            'city' => $data['city'] ?: null,
            'country' => $data['country'] ?: 'CH',
            'notes' => $data['notes'] ?: null,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE contacts SET type = :type, company_name = :company_name, first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, address = :address, plz = :plz, city = :city, country = :country, notes = :notes WHERE id = :id AND deleted_at IS NULL');
        $stmt->execute([
            'id' => $id,
            'type' => $data['type'],
            'company_name' => $data['company_name'] ?: null,
            'first_name' => $data['first_name'] ?: null,
            'last_name' => $data['last_name'] ?: null,
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,
            'plz' => $data['plz'] ?: null,
            'city' => $data['city'] ?: null,
            'country' => $data['country'] ?: 'CH',
            'notes' => $data['notes'] ?: null,
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE contacts SET deleted_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        $db = Database::getConnection();
        $stmt = $db->query('SELECT COUNT(*) as count FROM contacts WHERE deleted_at IS NULL');
        return (int) $stmt->fetch()['count'];
    }
}
