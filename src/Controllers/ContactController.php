<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Contact;
use App\Models\Quote;
use App\Models\Invoice;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

class ContactController
{
    public function index(): void
    {
        AuthMiddleware::check();

        $search = trim($_GET['search'] ?? '');
        $type = trim($_GET['type'] ?? '');

        $contacts = Contact::all($search, $type);
        $totalCount = Contact::count();

        $pageTitle = 'Kontakte';
        require __DIR__ . '/../Views/contacts/index.php';
    }

    public function create(): void
    {
        AuthMiddleware::check();

        $contact = [
            'type' => 'kunde',
            'company_name' => '',
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'plz' => '',
            'city' => '',
            'country' => 'CH',
            'notes' => '',
        ];
        $errors = $_SESSION['form_errors'] ?? [];
        $contact = $_SESSION['form_data'] ?? $contact;
        unset($_SESSION['form_errors'], $_SESSION['form_data']);

        $pageTitle = 'Neuer Kontakt';
        require __DIR__ . '/../Views/contacts/form.php';
    }

    public function store(): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $data = $this->validateData();

        if (!empty($data['errors'])) {
            $_SESSION['form_errors'] = $data['errors'];
            $_SESSION['form_data'] = $data['fields'];
            header('Location: /contacts/create');
            exit;
        }

        $id = Contact::create($data['fields']);
        $_SESSION['flash_success'] = 'Kontakt wurde erstellt.';
        header('Location: /contacts/' . $id);
        exit;
    }

    public function show(string $id): void
    {
        AuthMiddleware::check();

        $contact = Contact::find((int) $id);
        if (!$contact) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $quotes = Quote::getByContact((int) $id);
        $invoices = Invoice::getByContact((int) $id);

        $pageTitle = contactName($contact);
        require __DIR__ . '/../Views/contacts/show.php';
    }

    public function edit(string $id): void
    {
        AuthMiddleware::check();

        $contact = Contact::find((int) $id);
        if (!$contact) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $errors = $_SESSION['form_errors'] ?? [];
        $contact = $_SESSION['form_data'] ?? $contact;
        unset($_SESSION['form_errors'], $_SESSION['form_data']);

        $pageTitle = 'Kontakt bearbeiten';
        require __DIR__ . '/../Views/contacts/form.php';
    }

    public function update(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $existing = Contact::find((int) $id);
        if (!$existing) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $data = $this->validateData();

        if (!empty($data['errors'])) {
            $_SESSION['form_errors'] = $data['errors'];
            $_SESSION['form_data'] = $data['fields'];
            header('Location: /contacts/' . $id . '/edit');
            exit;
        }

        Contact::update((int) $id, $data['fields']);
        $_SESSION['flash_success'] = 'Kontakt wurde aktualisiert.';
        header('Location: /contacts/' . $id);
        exit;
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::check();
        CsrfMiddleware::verify();

        $contact = Contact::find((int) $id);
        if (!$contact) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        Contact::delete((int) $id);
        $_SESSION['flash_success'] = 'Kontakt wurde gelöscht.';
        header('Location: /contacts');
        exit;
    }

    private function validateData(): array
    {
        $errors = [];

        $fields = [
            'type' => trim($_POST['type'] ?? 'kunde'),
            'company_name' => trim($_POST['company_name'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'plz' => trim($_POST['plz'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'country' => trim($_POST['country'] ?? 'CH'),
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        if (!in_array($fields['type'], ['kunde', 'firma'], true)) {
            $errors['type'] = 'Ungültiger Typ.';
        }

        if ($fields['type'] === 'firma' && $fields['company_name'] === '') {
            $errors['company_name'] = 'Firmenname ist erforderlich.';
        }

        if ($fields['type'] === 'kunde' && $fields['last_name'] === '') {
            $errors['last_name'] = 'Nachname ist erforderlich.';
        }

        if ($fields['email'] !== '' && !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ungültige E-Mail-Adresse.';
        }

        return ['fields' => $fields, 'errors' => $errors];
    }
}
