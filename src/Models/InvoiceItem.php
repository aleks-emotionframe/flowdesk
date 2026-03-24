<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class InvoiceItem
{
    public static function getByInvoice(int $invoiceId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM invoice_items WHERE invoice_id = :invoice_id ORDER BY position ASC');
        $stmt->execute(['invoice_id' => $invoiceId]);
        return $stmt->fetchAll();
    }

    public static function replaceForInvoice(int $invoiceId, array $items): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('DELETE FROM invoice_items WHERE invoice_id = :invoice_id');
        $stmt->execute(['invoice_id' => $invoiceId]);

        $stmt = $db->prepare('INSERT INTO invoice_items (invoice_id, position, description, quantity, unit, unit_price, total) VALUES (:invoice_id, :position, :description, :quantity, :unit, :unit_price, :total)');

        foreach ($items as $i => $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $total = round($qty * $price, 2);

            $stmt->execute([
                'invoice_id' => $invoiceId,
                'position' => $i + 1,
                'description' => $item['description'],
                'quantity' => $qty,
                'unit' => $item['unit'],
                'unit_price' => $price,
                'total' => $total,
            ]);
        }
    }
}
