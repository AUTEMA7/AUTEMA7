<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Support\AuditLogger;
use App\Support\Auth;

final class HospitalizationController
{
    public static function index(): void
    {
        Auth::requireRole(['director', 'chief', 'staff']);
        $stmt = Database::connection()->prepare('SELECT * FROM admissions WHERE tenant_id = :tenant ORDER BY id DESC LIMIT 30');
        $stmt->execute(['tenant' => $_SESSION['user']['tenant_id']]);
        $admissions = $stmt->fetchAll();
        require __DIR__ . '/../../views/modules/hospitalization.php';
    }

    public static function store(): void
    {
        Auth::requireRole(['director', 'chief']);
        $stmt = Database::connection()->prepare('INSERT INTO admissions (tenant_id, npi, room_number, bed_number, status) VALUES (:tenant, :npi, :room, :bed, :status)');
        $stmt->execute([
            'tenant' => $_SESSION['user']['tenant_id'],
            'npi' => $_POST['npi'] ?? '',
            'room' => $_POST['room_number'] ?? '',
            'bed' => $_POST['bed_number'] ?? '',
            'status' => $_POST['status'] ?? 'active',
        ]);
        AuditLogger::log('ADMISSION_CREATED', 'admission', (string) Database::connection()->lastInsertId());
        header('Location: /modules/hospitalization');
    }
}
