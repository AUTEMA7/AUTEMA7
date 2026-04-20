<?php

declare(strict_types=1);

namespace App\Support;

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user']) && isset($_SESSION['session_verified']);
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /');
            exit;
        }
    }

    public static function requireRole(array $roles): void
    {
        self::requireAuth();
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            exit('Accès refusé.');
        }
    }

    public static function requiresServiceCode(): bool
    {
        return ($_SESSION['user']['role'] ?? '') !== 'director';
    }
}
