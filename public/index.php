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

// Request auflösen
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->resolve($method, $uri);
