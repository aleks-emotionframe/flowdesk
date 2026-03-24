<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class QuoteItem
{
    public static function getByQuote(int $quoteId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM quote_items WHERE quote_id = :quote_id ORDER BY position ASC');
        $stmt->execute(['quote_id' => $quoteId]);
        return $stmt->fetchAll();
    }

    public static function replaceForQuote(int $quoteId, array $items): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('DELETE FROM quote_items WHERE quote_id = :quote_id');
        $stmt->execute(['quote_id' => $quoteId]);

        $stmt = $db->prepare('INSERT INTO quote_items (quote_id, position, description, quantity, unit, unit_price, total) VALUES (:quote_id, :position, :description, :quantity, :unit, :unit_price, :total)');

        foreach ($items as $i => $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $total = round($qty * $price, 2);

            $stmt->execute([
                'quote_id' => $quoteId,
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
