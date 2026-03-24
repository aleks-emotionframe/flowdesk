# CLAUDE.md — FlowDesk Agentur Management Tool

## Projekt

FlowDesk ist ein webbasiertes Agentur-Management-Tool für Schweizer KMU/Agenturen. Reines PHP (kein Framework), MySQL, Vanilla JS + Tailwind CSS via CDN. Schweizer Lokalisierung (CHF, MwSt., QR-Rechnung).

## Tech Stack — STRIKT EINHALTEN

- **Backend:** PHP 8.1+, PDO MySQL, PSR-4 Autoloading via Composer
- **Frontend:** HTML, Tailwind CSS (CDN), Vanilla JavaScript (kein React, kein Vue, kein jQuery)
- **PDF:** dompdf/dompdf
- **E-Mail:** PHPMailer
- **Datum:** Carbon
- **Env:** vlucas/phpdotenv
- **Datenbank:** MySQL 8 / MariaDB 10.6+, utf8mb4
- **KEINE Frameworks** (kein Laravel, kein Symfony, kein WordPress)

## Architektur

- MVC-Pattern ohne Framework
- Entry Point: `public/index.php` mit einfachem Router
- Controllers in `src/Controllers/`
- Models in `src/Models/` (Active Record Pattern, jede Klasse = 1 Tabelle)
- Views in `src/Views/` als `.php` Templates
- Services in `src/Services/` für Business-Logik (PDF, E-Mail, Export)
- Middleware in `src/Middleware/` (Auth, CSRF)
- Config in `config/app.php`
- Migrations in `database/migrations/` als nummerierte SQL-Dateien

## Hosting / Deployment

- Hostpoint Shared Hosting (kein SSH, kein CLI Composer)
- vendor/ Ordner MUSS im Repo committed sein
- Claude Code muss nach jeder Composer-Änderung `composer install` ausführen und vendor/ committen
- Deployment via FTP-Upload oder GitHub ZIP-Download
- public/ ist das Webroot
- Datenbank-Setup via phpMyAdmin (SQL-Migrations importieren)
- PHP-Version auf Hostpoint: 8.1+
- .htaccess im public/ Ordner für URL-Rewriting (mod_rewrite ist auf Hostpoint verfügbar)

## Datenbank-Konventionen

- Tabellennamen: snake_case, Plural (z.B. `invoices`, `contacts`)
- Primärschlüssel: `id` (AUTO_INCREMENT)
- Fremdschlüssel: `tabelle_id` (z.B. `contact_id`)
- Timestamps: `created_at`, `updated_at` (DATETIME)
- Soft Delete: `deleted_at` (DATETIME NULL)
- Beträge: DECIMAL(10,2), immer in CHF
- MwSt-Satz: DECIMAL(4,2), Standard 8.1%

## Tabellen (Kern)

1. `users` — id, name, email, password_hash, role (admin/user), avatar, created_at, updated_at
2. `contacts` — id, type (kunde/firma), company_name, first_name, last_name, email, phone, address, plz, city, country (default CH), notes, created_at, updated_at, deleted_at
3. `quotes` (Offerten) — id, quote_number (OF-YYYY-NNN), contact_id, title, status (entwurf/gesendet/akzeptiert/abgelehnt), valid_until, subtotal, mwst_satz, mwst_betrag, total, notes, sent_at, created_at, updated_at
4. `quote_items` — id, quote_id, position, description, quantity, unit (Stk/Std/Pauschal), unit_price, total
5. `invoices` (Rechnungen) — id, invoice_number (RE-YYYY-NNN), contact_id, quote_id (NULL), title, status (entwurf/gesendet/offen/bezahlt/überfällig/storniert), due_date, subtotal, mwst_satz, mwst_betrag, total, paid_at, sent_at, reminder_count, created_at, updated_at
6. `invoice_items` — id, invoice_id, position, description, quantity, unit, unit_price, total
7. `payments` — id, invoice_id, amount, payment_date, method (bank/bar/kreditkarte), reference, notes, imported_at, created_at
8. `recurring_invoices` — id, contact_id, title, interval (monatlich/quartalsweise/jährlich), next_date, items_json, active, created_at, updated_at
9. `activities` — id, user_id, type, reference_type, reference_id, description, created_at
10. `settings` — id, key, value (für Firmendaten, SMTP, etc.)

## Nummerierung

- Offerten: OF-YYYY-NNN (z.B. OF-2026-001), fortlaufend pro Jahr
- Rechnungen: RE-YYYY-NNN (z.B. RE-2026-001), fortlaufend pro Jahr
- Mahnungen: MA-YYYY-NNN

## Features — Reihenfolge der Umsetzung

### Phase 1: Grundgerüst
1. Komplette Ordnerstruktur anlegen (src/Controllers, src/Models, src/Services, src/Middleware, src/Views, config, database/migrations, storage/exports, assets/css, assets/js, assets/img, tests)
2. composer install ausführen und vendor/ committen
3. Router, Autoloading, .env laden
4. Auth (Login/Logout, Session, Passwort-Hashing)
5. Layout-Template (Sidebar, Header, Dashboard-Shell)
6. Dashboard mit Statistik-Karten
7. Datenbank-Migrations-SQL-Dateien für ALLE Tabellen

### Phase 2: Kontakte
8. Kontakte CRUD (Liste, Erstellen, Bearbeiten, Löschen)
9. Kontakt-Detailseite mit verknüpften Offerten/Rechnungen
10. Suche und Filter

### Phase 3: Offerten
11. Offerten CRUD mit Positionen (dynamisch hinzufügen/entfernen)
12. PDF-Generierung (professionelles Layout mit Firmenlogo)
13. Offerte per E-Mail senden
14. Status-Workflow (Entwurf → Gesendet → Akzeptiert/Abgelehnt)
15. Offerte → Rechnung umwandeln (1-Klick)

### Phase 4: Rechnungen
16. Rechnungen CRUD mit Positionen
17. PDF mit Schweizer QR-Zahlschein (QR-Bill nach ISO 20022)
18. Rechnung per E-Mail senden
19. Zahlungseingänge erfassen
20. Automatische Fälligkeitsprüfung + Mahnwesen
21. Wiederkehrende Rechnungen

### Phase 5: Buchhaltung & Export
22. Umsatz-Dashboard (Monat/Quartal/Jahr Chart)
23. CSV-Export für Buchhaltungssoftware (kompatibel mit Bexio/Abacus Import)
24. MwSt-Abrechnung (Abrechnungsperiode wählbar)
25. Jahresabschluss-Export

### Phase 6: Feinschliff
26. Aktivitäten-Log
27. Einstellungen (Firmendaten, SMTP, Logo-Upload)
28. Benutzer-Verwaltung (falls mehrere User)

## UI/Design-Regeln — STRIKT EINHALTEN

- Design orientiert sich am bexio-ähnlichen Dashboard-Layout
- Saubere Sidebar links (dunkelblau/grau), weisser Content-Bereich
- Tailwind CSS Klassen, KEIN custom CSS ausser für PDF-Templates
- Farben: Primary #4F46E5 (Indigo), Success #22C55E, Warning #F59E0B, Danger #EF4444, Grau-Töne für Hintergrund
- Typografie: Inter (Google Fonts CDN)
- Karten mit rounded-xl shadow-sm border border-gray-100 bg-white p-6
- Tabellen: schlicht, divide-y, hover-Effekt auf Zeilen
- Status-Badges: farbige Punkte + Text (z.B. grün "Bezahlt", rot "Überfällig", blau "Gesendet", grau "Entwurf")
- Responsive: funktioniert auf Desktop und Tablet (Mobile nice-to-have)
- Sidebar-Navigation mit Icons (Heroicons CDN oder inline SVG)
- Schweizer Zahlenformat: 1'000.00 (Apostroph als Tausendertrennzeichen)
- Dashboard zeigt: Offene Rechnungen (CHF), Umsatz aktueller Monat, Offene Offerten (Anzahl), Überfällige (Anzahl mit Warnung)
- Umsatz-Chart (Bar-Chart) mit Monat/Quartal/Jahr Toggle
- Schnellaktionen-Panel rechts (Neue Offerte, Neue Rechnung, Neuer Kunde, etc.)
- Letzte Rechnungen und Letzte Aktivitäten als Listen unten

## Code-Regeln

- KEIN over-engineering. Einfach, lesbar, funktional.
- Jede Datei hat EINEN klaren Zweck.
- SQL Queries: Prepared Statements, IMMER.
- Fehlerbehandlung: try/catch bei DB-Operationen, saubere Fehlermeldungen.
- Validierung: Server-seitig, immer. Client-seitig als Bonus.
- CSRF-Token bei allen POST-Formularen.
- Passwörter: password_hash() / password_verify(), NIEMALS Klartext.
- Sessions: session_regenerate_id() nach Login.
- Alle Beträge werden serverseitig berechnet, NIE vom Client übernommen.
- PHP Dateien: strict_types=1, Typ-Deklarationen wo möglich.

## PDF-Regeln

- DIN A4 Format
- Firmenlogo oben links, Firmenangaben oben rechts
- Empfänger links unter Logo
- Dokumentnummer, Datum, Fälligkeitsdatum rechts
- Positionstabelle: Pos | Beschreibung | Menge | Einheit | Einzelpreis | Total
- Zwischensumme, MwSt., Gesamttotal
- QR-Zahlschein unten (nur bei Rechnungen) gemäss Swiss QR-Bill Standard
- Zahlungsbedingungen als Text

## Was NICHT gemacht werden soll

- KEIN REST-API (nur serverseitiges Rendering)
- KEIN SPA, KEIN JavaScript-Framework
- KEIN Docker/Container-Setup
- KEINE Unit-Tests (später separat)
- KEIN Multi-Tenancy / Multi-Währung
- KEINE Zeiterfassung
- KEINE Projektverwaltung
- KEINE Lagerverwaltung
- KEIN kompliziertes Rechte-System (nur admin/user reicht)
