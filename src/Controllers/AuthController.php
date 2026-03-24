<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Middleware\CsrfMiddleware;

class AuthController
{
    public function loginForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        CsrfMiddleware::verify();

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $_SESSION['login_error'] = 'Bitte Benutzername eingeben.';
            header('Location: /login');
            exit;
        }

        // Temporärer Test-Login ohne Passwort und ohne DB
        if ($email === 'admin') {
            session_regenerate_id(true);
            $_SESSION['user_id'] = 1;
            $_SESSION['user'] = [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@flowdesk.ch',
                'role' => 'admin',
                'avatar' => null,
            ];
            header('Location: /');
            exit;
        }

        $_SESSION['login_error'] = 'Ungültiger Benutzername.';
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        header('Location: /login');
        exit;
    }
}
