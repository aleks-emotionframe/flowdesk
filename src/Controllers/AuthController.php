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
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Bitte E-Mail und Passwort eingeben.';
            header('Location: /login');
            exit;
        }

        $user = User::findByEmail($email);

        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            $_SESSION['login_error'] = 'Ungültige E-Mail oder Passwort.';
            header('Location: /login');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'avatar' => $user['avatar'],
        ];

        header('Location: /');
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
