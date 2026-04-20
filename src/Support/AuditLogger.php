<?php

declare(strict_types=1);

namespace App\Support;

use App\Database;

final class AuditLogger
{
    public static function log(string $action, string $entityType, ?string $entityId = null, array $meta = []): void
    {
        $userId = $_SESSION['user']['id'] ?? null;
        $tenantId = $_SESSION['user']['tenant_id'] ?? null;

        $stmt = Database::connection()->prepare(
            'INSERT INTO audit_logs (tenant_id, user_id, action, entity_type, entity_id, metadata, event_time_wat) VALUES (:tenant_id, :user_id, :action, :entity_type, :entity_id, :metadata, CONVERT_TZ(NOW(), "+00:00", "+01:00"))'
        );

        $stmt->execute([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
