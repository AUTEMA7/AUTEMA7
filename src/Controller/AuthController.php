<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Support\AuditLogger;
use App\Support\Auth;
use App\Support\OtpService;

final class AuthController
{
    public static function showLogin(): void
    {
        require __DIR__ . '/../../views/auth/login.php';
    }

    public static function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $stmt = Database::connection()->prepare('SELECT id, tenant_id, full_name, role, email, phone, password_hash, service_id FROM users WHERE email = :email AND is_active = 1 LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $_SESSION['flash_error'] = 'Identifiants invalides.';
            header('Location: /');
            return;
        }

        $_SESSION['auth_pending'] = $user;
        OtpService::issue((int) $user['id'], (string) $user['phone']);
        header('Location: /otp');
    }

    public static function showOtp(): void
    {
        if (!isset($_SESSION['auth_pending'])) {
            header('Location: /');
            return;
        }
        require __DIR__ . '/../../views/auth/otp.php';
    }

    public static function verifyOtp(): void
    {
        $user = $_SESSION['auth_pending'] ?? null;
        if (!$user) {
            header('Location: /');
            return;
        }

        $otp = trim((string) ($_POST['otp'] ?? ''));
        if (!OtpService::verify((int) $user['id'], $otp)) {
            $_SESSION['flash_error'] = 'OTP invalide ou expiré.';
            header('Location: /otp');
            return;
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'tenant_id' => (int) $user['tenant_id'],
            'name' => $user['full_name'],
            'role' => $user['role'],
            'email' => $user['email'],
            'service_id' => $user['service_id'],
        ];

        if (Auth::requiresServiceCode()) {
            header('Location: /service-access');
            return;
        }

        $_SESSION['session_verified'] = true;
        unset($_SESSION['auth_pending']);
        AuditLogger::log('LOGIN_2FA_SUCCESS', 'user', (string) $user['id']);
        header('Location: /dashboard');
    }

    public static function showServiceAccess(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            return;
        }
        require __DIR__ . '/../../views/auth/service-access.php';
    }

    public static function verifyServiceAccess(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            return;
        }

        $serviceCode = trim((string) ($_POST['service_code'] ?? ''));
        $stmt = Database::connection()->prepare('SELECT access_code FROM services WHERE id = :id AND tenant_id = :tenant_id');
        $stmt->execute([
            'id' => $_SESSION['user']['service_id'],
            'tenant_id' => $_SESSION['user']['tenant_id'],
        ]);
        $service = $stmt->fetch();

        if (!$service || !password_verify($serviceCode, (string) $service['access_code'])) {
            $_SESSION['flash_error'] = 'Code service invalide.';
            header('Location: /service-access');
            return;
        }

        $_SESSION['session_verified'] = true;
        unset($_SESSION['auth_pending']);
        AuditLogger::log('SERVICE_ACCESS_GRANTED', 'service', (string) $_SESSION['user']['service_id']);
        header('Location: /dashboard');
    }

    public static function logout(): void
    {
        AuditLogger::log('LOGOUT', 'user', (string) ($_SESSION['user']['id'] ?? '0'));
        session_destroy();
        header('Location: /');
    }
}
