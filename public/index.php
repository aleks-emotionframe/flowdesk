<?php

declare(strict_types=1);

// Fehler anzeigen (temporär für Debugging)
error_reporting(E_ALL);
ini_set('display_errors', '1');

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

// Request auflösen
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->resolve($method, $uri);
