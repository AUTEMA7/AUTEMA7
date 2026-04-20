<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Support\AuditLogger;
use App\Support\Auth;

final class LabController
{
    public static function index(): void
    {
        Auth::requireRole(['director', 'chief', 'staff']);
        $stmt = Database::connection()->prepare('SELECT id, npi, test_name, result_status, created_at FROM lab_requests WHERE tenant_id = :tenant ORDER BY id DESC LIMIT 30');
        $stmt->execute(['tenant' => $_SESSION['user']['tenant_id']]);
        $requests = $stmt->fetchAll();
        require __DIR__ . '/../../views/modules/lab.php';
    }

    public static function store(): void
    {
        Auth::requireRole(['director', 'chief', 'staff']);
        $stmt = Database::connection()->prepare('INSERT INTO lab_requests (tenant_id, npi, test_name, result_status) VALUES (:tenant, :npi, :test_name, :result_status)');
        $stmt->execute([
            'tenant' => $_SESSION['user']['tenant_id'],
            'npi' => $_POST['npi'] ?? '',
            'test_name' => $_POST['test_name'] ?? '',
            'result_status' => $_POST['result_status'] ?? 'pending',
        ]);
        AuditLogger::log('LAB_REQUEST_CREATED', 'lab_request', (string) Database::connection()->lastInsertId());
        header('Location: /modules/lab');
    }
}
