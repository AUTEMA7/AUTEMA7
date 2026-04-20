<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Support\Auth;

final class DashboardController
{
    public static function index(): void
    {
        Auth::requireAuth();
        $tenantId = (int) $_SESSION['user']['tenant_id'];

        $patientsStmt = Database::connection()->prepare('SELECT COUNT(*) as c FROM patients WHERE tenant_id = :tenant');
        $patientsStmt->execute(['tenant' => $tenantId]);
        $patients = (int) ($patientsStmt->fetch()['c'] ?? 0);

        $auditStmt = Database::connection()->prepare('SELECT action, entity_type, event_time_wat FROM audit_logs WHERE tenant_id = :tenant ORDER BY id DESC LIMIT 8');
        $auditStmt->execute(['tenant' => $tenantId]);
        $auditLogs = $auditStmt->fetchAll();

        require __DIR__ . '/../../views/dashboard/index.php';
    }
}
