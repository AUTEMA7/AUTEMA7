<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Support\AuditLogger;
use App\Support\Auth;

final class PharmacyController
{
    public static function index(): void
    {
        Auth::requireRole(['director', 'chief', 'staff']);
        $stmt = Database::connection()->prepare('SELECT * FROM pharmacy_orders WHERE tenant_id = :tenant ORDER BY id DESC LIMIT 30');
        $stmt->execute(['tenant' => $_SESSION['user']['tenant_id']]);
        $orders = $stmt->fetchAll();
        require __DIR__ . '/../../views/modules/pharmacy.php';
    }

    public static function sendOrder(): void
    {
        Auth::requireRole(['director', 'chief', 'staff']);
        $token = bin2hex(random_bytes(8));
        $stmt = Database::connection()->prepare('INSERT INTO pharmacy_orders (tenant_id, npi, medication, pharmacy_name, token, status) VALUES (:tenant, :npi, :medication, :pharmacy, :token, :status)');
        $stmt->execute([
            'tenant' => $_SESSION['user']['tenant_id'],
            'npi' => $_POST['npi'] ?? '',
            'medication' => $_POST['medication'] ?? '',
            'pharmacy' => $_POST['pharmacy_name'] ?? '',
            'token' => $token,
            'status' => 'reserved_2h',
        ]);
        AuditLogger::log('PHARMACY_ORDER_SENT', 'pharmacy_order', (string) Database::connection()->lastInsertId(), ['token' => $token]);
        header('Location: /modules/pharmacy');
    }
}
