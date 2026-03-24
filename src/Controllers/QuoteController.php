<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

class QuoteController
{
    public function index(): void
    {
        AuthMiddleware::check();

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $quotes = Quote::all($search, $status);

        $pageTitle = 'Offerten';
        require __DIR__ . '/../Views/quotes/index.php';
    }

    public function create(): void
    {
        AuthMiddleware::check();

        $contacts = Contact::all();
        $contactId = (int) ($_GET['contact_id'] ?? 0);
        $config = require __DIR__ . '/../../config/app.php';
        $mwstSatz = $config['mwst_satz'];

        $quote = [
            'contact_id' => $contactId,
            'title' => '',
            'valid_until' => date('Y-m-d', strtotime('+30 days')),
            'mwst_satz' => $mwstSatz,
            'notes' => '',
        ];
        $items = [['description' => '', 'quantity' => '1', 'unit' => 'Std', 'unit_price' => '']];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);

        $pageTitle = 'Neue Offerte';
        require __DIR__ . '/../Views/quotes/form.php';
    }

    public function store(): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $data = $this->validateData();

        if (!empty($data['errors'])) {
            $_SESSION['form_errors'] = $data['errors'];
            header('Location: /quotes/create');
            exit;
        }

        $id = Quote::create($data['fields']);
        QuoteItem::replaceForQuote($id, $data['items']);

        // Totale neu berechnen und speichern
        $this->recalcTotals($id, $data['items'], (float) $data['fields']['mwst_satz']);

        $_SESSION['flash_success'] = 'Offerte wurde erstellt.';
        header('Location: /quotes/' . $id);
        exit;
    }

    public function show(string $id): void
    {
        AuthMiddleware::check();

        $quote = Quote::find((int) $id);
        if (!$quote) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $items = QuoteItem::getByQuote((int) $id);
        $pageTitle = $quote['quote_number'];
        require __DIR__ . '/../Views/quotes/show.php';
    }

    public function edit(string $id): void
    {
        AuthMiddleware::check();

        $quote = Quote::find((int) $id);
        if (!$quote) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $contacts = Contact::all();
        $items = QuoteItem::getByQuote((int) $id);
        if (empty($items)) {
            $items = [['description' => '', 'quantity' => '1', 'unit' => 'Std', 'unit_price' => '']];
        }
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);

        $pageTitle = 'Offerte bearbeiten';
        require __DIR__ . '/../Views/quotes/form.php';
    }

    public function update(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $existing = Quote::find((int) $id);
        if (!$existing) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $data = $this->validateData();

        if (!empty($data['errors'])) {
            $_SESSION['form_errors'] = $data['errors'];
            header('Location: /quotes/' . $id . '/edit');
            exit;
        }

        Quote::update((int) $id, $data['fields']);
        QuoteItem::replaceForQuote((int) $id, $data['items']);
        $this->recalcTotals((int) $id, $data['items'], (float) $data['fields']['mwst_satz']);

        $_SESSION['flash_success'] = 'Offerte wurde aktualisiert.';
        header('Location: /quotes/' . $id);
        exit;
    }

    public function status(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $newStatus = $_POST['status'] ?? '';
        if (!in_array($newStatus, ['entwurf', 'gesendet', 'akzeptiert', 'abgelehnt'], true)) {
            header('Location: /quotes/' . $id);
            exit;
        }

        Quote::updateStatus((int) $id, $newStatus);
        $_SESSION['flash_success'] = 'Status wurde aktualisiert.';
        header('Location: /quotes/' . $id);
        exit;
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        Quote::delete((int) $id);
        $_SESSION['flash_success'] = 'Offerte wurde gelöscht.';
        header('Location: /quotes');
        exit;
    }

    public function toInvoice(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $quote = Quote::find((int) $id);
        if (!$quote) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $quoteItems = QuoteItem::getByQuote((int) $id);

        $invoiceId = Invoice::create([
            'contact_id' => $quote['contact_id'],
            'quote_id' => (int) $id,
            'title' => $quote['title'],
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'subtotal' => $quote['subtotal'],
            'mwst_satz' => $quote['mwst_satz'],
            'mwst_betrag' => $quote['mwst_betrag'],
            'total' => $quote['total'],
        ]);

        InvoiceItem::replaceForInvoice($invoiceId, $quoteItems);

        $_SESSION['flash_success'] = 'Rechnung wurde aus Offerte erstellt.';
        header('Location: /invoices/' . $invoiceId);
        exit;
    }

    private function validateData(): array
    {
        $errors = [];

        $fields = [
            'contact_id' => (int) ($_POST['contact_id'] ?? 0),
            'title' => trim($_POST['title'] ?? ''),
            'valid_until' => trim($_POST['valid_until'] ?? ''),
            'mwst_satz' => (float) ($_POST['mwst_satz'] ?? 8.1),
            'notes' => trim($_POST['notes'] ?? ''),
            'subtotal' => 0,
            'mwst_betrag' => 0,
            'total' => 0,
        ];

        if ($fields['contact_id'] <= 0) {
            $errors['contact_id'] = 'Bitte Kontakt auswählen.';
        }
        if ($fields['title'] === '') {
            $errors['title'] = 'Titel ist erforderlich.';
        }

        // Items parsen
        $items = [];
        $descriptions = $_POST['item_description'] ?? [];
        $quantities = $_POST['item_quantity'] ?? [];
        $units = $_POST['item_unit'] ?? [];
        $prices = $_POST['item_unit_price'] ?? [];

        for ($i = 0; $i < count($descriptions); $i++) {
            $desc = trim($descriptions[$i] ?? '');
            if ($desc === '') continue;
            $items[] = [
                'description' => $desc,
                'quantity' => $quantities[$i] ?? '1',
                'unit' => $units[$i] ?? 'Std',
                'unit_price' => $prices[$i] ?? '0',
            ];
        }

        if (empty($items)) {
            $errors['items'] = 'Mindestens eine Position ist erforderlich.';
        }

        return ['fields' => $fields, 'items' => $items, 'errors' => $errors];
    }

    private function recalcTotals(int $id, array $items, float $mwstSatz): void
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += round((float) $item['quantity'] * (float) $item['unit_price'], 2);
        }
        $mwstBetrag = round($subtotal * $mwstSatz / 100, 2);
        $total = $subtotal + $mwstBetrag;

        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare('UPDATE quotes SET subtotal = :subtotal, mwst_betrag = :mwst_betrag, total = :total WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'subtotal' => $subtotal,
            'mwst_betrag' => $mwstBetrag,
            'total' => $total,
        ]);
    }
}
