<?php

declare(strict_types=1);

// Autoloading
require_once __DIR__ . '/../vendor/autoload.php';

// .env laden
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Helpers laden
require_once __DIR__ . '/../src/helpers.php';

// Session starten
session_start();

// CSRF-Token generieren
App\Middleware\CsrfMiddleware::generateToken();

// Router konfigurieren
$router = new App\Router();

// Auth-Routen (öffentlich)
$router->get('/login', [App\Controllers\AuthController::class, 'loginForm']);
$router->post('/login', [App\Controllers\AuthController::class, 'login']);
$router->get('/logout', [App\Controllers\AuthController::class, 'logout']);

// Dashboard (geschützt)
$router->get('/', [App\Controllers\DashboardController::class, 'index']);

// Kontakte
$router->get('/contacts', [App\Controllers\ContactController::class, 'index']);
$router->get('/contacts/create', [App\Controllers\ContactController::class, 'create']);
$router->post('/contacts', [App\Controllers\ContactController::class, 'store']);
$router->get('/contacts/{id}', [App\Controllers\ContactController::class, 'show']);
$router->get('/contacts/{id}/edit', [App\Controllers\ContactController::class, 'edit']);
$router->post('/contacts/{id}/update', [App\Controllers\ContactController::class, 'update']);
$router->post('/contacts/{id}/delete', [App\Controllers\ContactController::class, 'destroy']);

// Offerten
$router->get('/quotes', [App\Controllers\QuoteController::class, 'index']);
$router->get('/quotes/create', [App\Controllers\QuoteController::class, 'create']);
$router->post('/quotes', [App\Controllers\QuoteController::class, 'store']);
$router->get('/quotes/{id}', [App\Controllers\QuoteController::class, 'show']);
$router->get('/quotes/{id}/edit', [App\Controllers\QuoteController::class, 'edit']);
$router->post('/quotes/{id}/update', [App\Controllers\QuoteController::class, 'update']);
$router->post('/quotes/{id}/status', [App\Controllers\QuoteController::class, 'status']);
$router->post('/quotes/{id}/delete', [App\Controllers\QuoteController::class, 'destroy']);
$router->post('/quotes/{id}/to-invoice', [App\Controllers\QuoteController::class, 'toInvoice']);

// Rechnungen
$router->get('/invoices', [App\Controllers\InvoiceController::class, 'index']);
$router->get('/invoices/create', [App\Controllers\InvoiceController::class, 'create']);
$router->post('/invoices', [App\Controllers\InvoiceController::class, 'store']);
$router->get('/invoices/{id}', [App\Controllers\InvoiceController::class, 'show']);
$router->get('/invoices/{id}/edit', [App\Controllers\InvoiceController::class, 'edit']);
$router->post('/invoices/{id}/update', [App\Controllers\InvoiceController::class, 'update']);
$router->post('/invoices/{id}/status', [App\Controllers\InvoiceController::class, 'status']);
$router->post('/invoices/{id}/delete', [App\Controllers\InvoiceController::class, 'destroy']);

// Request auflösen
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->resolve($method, $uri);
