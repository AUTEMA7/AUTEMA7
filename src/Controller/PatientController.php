<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;

final class PatientController
{
    public static function index(): void
    {
        self::guard();
        $patients = Database::connection()->query('SELECT id, npi, full_name, phone, created_at FROM patients ORDER BY id DESC')->fetchAll();
        require __DIR__ . '/../../views/patients/index.php';
    }

    public static function store(): void
    {
        self::guard();
        $sql = 'INSERT INTO patients (npi, full_name, phone, patient_code) VALUES (:npi, :full_name, :phone, :patient_code)';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'npi' => $_POST['npi'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'patient_code' => (string) random_int(100000, 99999999),
        ]);

        header('Location: /dashboard');
    }

    private static function guard(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
    }
}
