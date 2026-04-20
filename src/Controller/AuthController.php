<?php

declare(strict_types=1);

namespace App\Controller;

final class AuthController
{
    public static function showLogin(): void
    {
        require __DIR__ . '/../../views/auth/login.php';
    }

    public static function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($email === 'admin@ilaranet.bj' && $password === 'admin123') {
            $_SESSION['user'] = [
                'name' => 'Directeur Demo',
                'role' => 'directeur',
                'email' => $email,
            ];
            header('Location: /dashboard');
            return;
        }

        $_SESSION['flash_error'] = 'Identifiants invalides. Utilisez admin@ilaranet.bj / admin123 pour la démo.';
        header('Location: /');
    }

    public static function logout(): void
    {
        session_destroy();
        header('Location: /');
    }
}
