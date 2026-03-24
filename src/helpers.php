<?php

declare(strict_types=1);

/**
 * Formatiert einen Betrag im Schweizer Format (1'000.00)
 */
function formatCHF(float $amount): string
{
    $formatted = number_format($amount, 2, '.', "'");
    return 'CHF ' . $formatted;
}

/**
 * Formatiert eine Zahl im Schweizer Format
 */
function formatNumber(float $number, int $decimals = 2): string
{
    return number_format($number, $decimals, '.', "'");
}

/**
 * Formatiert ein Datum für die Anzeige
 */
function formatDate(?string $date): string
{
    if (empty($date)) {
        return '-';
    }
    return date('d.m.Y', strtotime($date));
}

/**
 * Formatiert Datum und Zeit für die Anzeige
 */
function formatDateTime(?string $datetime): string
{
    if (empty($datetime)) {
        return '-';
    }
    return date('d.m.Y H:i', strtotime($datetime));
}

/**
 * Gibt den Kontaktnamen zurück
 */
function contactName(array $contact): string
{
    if (!empty($contact['company_name'])) {
        return $contact['company_name'];
    }
    return trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
}

/**
 * Gibt CSS-Klassen für Status-Badges zurück
 */
function statusBadge(string $status): string
{
    $badges = [
        'entwurf' => 'bg-gray-100 text-gray-700',
        'gesendet' => 'bg-blue-100 text-blue-700',
        'akzeptiert' => 'bg-green-100 text-green-700',
        'abgelehnt' => 'bg-red-100 text-red-700',
        'offen' => 'bg-yellow-100 text-yellow-700',
        'bezahlt' => 'bg-green-100 text-green-700',
        'überfällig' => 'bg-red-100 text-red-700',
        'storniert' => 'bg-gray-100 text-gray-500',
    ];
    $class = $badges[$status] ?? 'bg-gray-100 text-gray-700';
    $label = ucfirst($status);
    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $class . '">' . htmlspecialchars($label) . '</span>';
}

/**
 * Escapet HTML-Output
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
