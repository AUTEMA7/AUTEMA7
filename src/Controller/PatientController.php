<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Support\AuditLogger;
use App\Support\Auth;

final class PatientController
{
    public static function index(): void
    {
        Auth::requireAuth();
        $stmt = Database::connection()->prepare('SELECT id, npi, full_name, phone, created_at FROM patients WHERE tenant_id = :tenant ORDER BY id DESC');
        $stmt->execute(['tenant' => $_SESSION['user']['tenant_id']]);
        $patients = $stmt->fetchAll();
        require __DIR__ . '/../../views/patients/index.php';
    }

    public static function store(): void
    {
        Auth::requireRole(['director', 'chief']);

        $stmt = Database::connection()->prepare(
            'INSERT INTO patients (tenant_id, npi, full_name, phone, patient_code) VALUES (:tenant_id, :npi, :full_name, :phone, :patient_code)'
        );
        $stmt->execute([
            'tenant_id' => $_SESSION['user']['tenant_id'],
            'npi' => $_POST['npi'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'patient_code' => (string) random_int(100000, 99999999),
        ]);

        AuditLogger::log('PATIENT_CREATED', 'patient', (string) Database::connection()->lastInsertId(), [
            'npi' => $_POST['npi'] ?? null,
        ]);

        header('Location: /patients');
    }
}
