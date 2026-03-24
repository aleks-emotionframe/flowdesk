<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Contact;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

class InvoiceController
{
    public function index(): void
    {
        AuthMiddleware::check();

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $invoices = Invoice::all($search, $status);

        $pageTitle = 'Rechnungen';
        require __DIR__ . '/../Views/invoices/index.php';
    }

    public function create(): void
    {
        AuthMiddleware::check();

        $contacts = Contact::all();
        $contactId = (int) ($_GET['contact_id'] ?? 0);
        $config = require __DIR__ . '/../../config/app.php';
        $mwstSatz = $config['mwst_satz'];

        $invoice = [
            'contact_id' => $contactId,
            'title' => '',
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'mwst_satz' => $mwstSatz,
        ];
        $items = [['description' => '', 'quantity' => '1', 'unit' => 'Std', 'unit_price' => '']];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);

        $pageTitle = 'Neue Rechnung';
        require __DIR__ . '/../Views/invoices/form.php';
    }

    public function store(): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $data = $this->validateData();

        if (!empty($data['errors'])) {
            $_SESSION['form_errors'] = $data['errors'];
            header('Location: /invoices/create');
            exit;
        }

        $id = Invoice::create($data['fields']);
        InvoiceItem::replaceForInvoice($id, $data['items']);
        $this->recalcTotals($id, $data['items'], (float) $data['fields']['mwst_satz']);

        $_SESSION['flash_success'] = 'Rechnung wurde erstellt.';
        header('Location: /invoices/' . $id);
        exit;
    }

    public function show(string $id): void
    {
        AuthMiddleware::check();

        $invoice = Invoice::find((int) $id);
        if (!$invoice) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $items = InvoiceItem::getByInvoice((int) $id);
        $pageTitle = $invoice['invoice_number'];
        require __DIR__ . '/../Views/invoices/show.php';
    }

    public function edit(string $id): void
    {
        AuthMiddleware::check();

        $invoice = Invoice::find((int) $id);
        if (!$invoice) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $contacts = Contact::all();
        $items = InvoiceItem::getByInvoice((int) $id);
        if (empty($items)) {
            $items = [['description' => '', 'quantity' => '1', 'unit' => 'Std', 'unit_price' => '']];
        }
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);

        $pageTitle = 'Rechnung bearbeiten';
        require __DIR__ . '/../Views/invoices/form.php';
    }

    public function update(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $existing = Invoice::find((int) $id);
        if (!$existing) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $data = $this->validateData();

        if (!empty($data['errors'])) {
            $_SESSION['form_errors'] = $data['errors'];
            header('Location: /invoices/' . $id . '/edit');
            exit;
        }

        Invoice::update((int) $id, $data['fields']);
        InvoiceItem::replaceForInvoice((int) $id, $data['items']);
        $this->recalcTotals((int) $id, $data['items'], (float) $data['fields']['mwst_satz']);

        $_SESSION['flash_success'] = 'Rechnung wurde aktualisiert.';
        header('Location: /invoices/' . $id);
        exit;
    }

    public function status(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $newStatus = $_POST['status'] ?? '';
        if (!in_array($newStatus, ['entwurf', 'gesendet', 'offen', 'bezahlt', 'überfällig', 'storniert'], true)) {
            header('Location: /invoices/' . $id);
            exit;
        }

        Invoice::updateStatus((int) $id, $newStatus);
        $_SESSION['flash_success'] = 'Status wurde aktualisiert.';
        header('Location: /invoices/' . $id);
        exit;
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        Invoice::delete((int) $id);
        $_SESSION['flash_success'] = 'Rechnung wurde gelöscht.';
        header('Location: /invoices');
        exit;
    }

    private function validateData(): array
    {
        $errors = [];

        $fields = [
            'contact_id' => (int) ($_POST['contact_id'] ?? 0),
            'title' => trim($_POST['title'] ?? ''),
            'due_date' => trim($_POST['due_date'] ?? ''),
            'mwst_satz' => (float) ($_POST['mwst_satz'] ?? 8.1),
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
        $stmt = $db->prepare('UPDATE invoices SET subtotal = :subtotal, mwst_betrag = :mwst_betrag, total = :total WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'subtotal' => $subtotal,
            'mwst_betrag' => $mwstBetrag,
            'total' => $total,
        ]);
    }
}
