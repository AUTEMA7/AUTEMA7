<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Support\AuditLogger;
use App\Support\Auth;

final class EmergencyController
{
    public static function index(): void
    {
        Auth::requireRole(['director', 'chief', 'staff']);
        $stmt = Database::connection()->prepare('SELECT * FROM emergency_cases WHERE tenant_id = :tenant ORDER BY id DESC LIMIT 30');
        $stmt->execute(['tenant' => $_SESSION['user']['tenant_id']]);
        $cases = $stmt->fetchAll();
        require __DIR__ . '/../../views/modules/emergency.php';
    }

    public static function store(): void
    {
        Auth::requireRole(['director', 'chief', 'staff']);
        $stmt = Database::connection()->prepare('INSERT INTO emergency_cases (tenant_id, npi, esi_level, priority_color, notes) VALUES (:tenant, :npi, :esi, :color, :notes)');
        $stmt->execute([
            'tenant' => $_SESSION['user']['tenant_id'],
            'npi' => $_POST['npi'] ?? '',
            'esi' => (int) ($_POST['esi_level'] ?? 3),
            'color' => $_POST['priority_color'] ?? 'jaune',
            'notes' => $_POST['notes'] ?? '',
        ]);
        AuditLogger::log('EMERGENCY_CASE_CREATED', 'emergency_case', (string) Database::connection()->lastInsertId());
        header('Location: /modules/emergency');
    }
}
