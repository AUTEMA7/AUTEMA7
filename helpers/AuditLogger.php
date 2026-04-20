<?php
/**
 * IlaraNet Bénin - Audit Trail Logger
 * Enregistrement immuable de toutes les actions
 */

namespace IlaraNet\Helpers;

use IlaraNet\Core\Database;

class AuditLogger
{
    private Database $db;
    private array $config;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->config = require __DIR__ . '/../config/app.php';
    }

    /**
     * Log une action dans l'audit trail
     */
    public function log(
        string $action,
        string $entity,
        ?int $entityId,
        string $details = '',
        mixed $oldValue = null,
        mixed $newValue = null
    ): int {
        $user = $_SESSION['user'] ?? null;
        $facilityId = $user['facility_id'] ?? null;
        
        $data = [
            'user_id' => $user['id'] ?? null,
            'user_name' => $user ? "{$user['nom']} {$user['prenom']}" : 'Système',
            'user_role' => $user['role'] ?? 'anonymous',
            'facility_id' => $facilityId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'details' => $details,
            'old_value' => $oldValue ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : null,
            'new_value' => $newValue ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => Security::getIpAddress(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('audit_logs', $data);
    }

    /**
     * Log une connexion utilisateur
     */
    public function logLogin(int $userId, bool $success, string $reason = ''): void
    {
        $this->log(
            $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED',
            'user',
            $userId,
            $reason,
            null,
            ['success' => $success]
        );
    }

    /**
     * Log une tentative d'accès non autorisé
     */
    public function logUnauthorizedAccess(string $resource, string $reason): void
    {
        $this->log(
            'UNAUTHORIZED_ACCESS',
            'system',
            null,
            "Tentative d'accès à: {$resource} - Raison: {$reason}"
        );
    }

    /**
     * Log une modification de donnée sensible
     */
    public function logSensitiveChange(
        string $entity,
        int $entityId,
        string $field,
        mixed $oldValue,
        mixed $newValue
    ): void {
        $this->log(
            'SENSITIVE_CHANGE',
            $entity,
            $entityId,
            "Champ modifié: {$field}",
            [$field => $oldValue],
            [$field => $newValue]
        );
    }

    /**
     * Log une action médicale critique
     */
    public function logCriticalMedicalAction(
        string $action,
        int $patientFileId,
        string $details
    ): void {
        $this->log(
            $action,
            'patient_file',
            $patientFileId,
            $details,
            null,
            ['critical' => true]
        );
    }

    /**
     * Log une transaction financière
     */
    public function logFinancialTransaction(
        string $type,
        int $invoiceId,
        float $amount,
        string $method
    ): void {
        $this->log(
            'FINANCIAL_TRANSACTION',
            'invoice',
            $invoiceId,
            "Type: {$type}, Montant: {$amount} FCFA, Méthode: {$method}",
            null,
            ['amount' => $amount, 'method' => $method]
        );
    }

    /**
     * Log une export de données
     */
    public function logDataExport(string $exportType, int $recordCount): void
    {
        $this->log(
            'DATA_EXPORT',
            'system',
            null,
            "Export {$exportType} - {$recordCount} enregistrements"
        );
    }

    /**
     * Récupère les logs pour un utilisateur
     */
    public function getUserLogs(int $userId, int $limit = 100): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM audit_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    /**
     * Récupère les logs pour un établissement
     */
    public function getFacilityLogs(int $facilityId, int $limit = 100): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM audit_logs WHERE facility_id = ? ORDER BY created_at DESC LIMIT ?",
            [$facilityId, $limit]
        );
    }

    /**
     * Récupère les logs pour une entité spécifique
     */
    public function getEntityLogs(string $entity, ?int $entityId = null, int $limit = 100): array
    {
        $sql = "SELECT * FROM audit_logs WHERE entity = ?";
        $params = [$entity];

        if ($entityId !== null) {
            $sql .= " AND entity_id = ?";
            $params[] = $entityId;
        }

        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Recherche dans les logs
     */
    public function searchLogs(
        ?string $keyword = null,
        ?string $action = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $facilityId = null,
        int $limit = 100
    ): array {
        $conditions = [];
        $params = [];

        if ($keyword) {
            $conditions[] = "(details LIKE ? OR user_name LIKE ?)";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }

        if ($action) {
            $conditions[] = "action = ?";
            $params[] = $action;
        }

        if ($startDate) {
            $conditions[] = "created_at >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $conditions[] = "created_at <= ?";
            $params[] = $endDate;
        }

        if ($facilityId) {
            $conditions[] = "facility_id = ?";
            $params[] = $facilityId;
        }

        $where = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : '';
        
        $sql = "SELECT * FROM audit_logs {$where} ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Exporte les logs pour audit externe
     */
    public function exportLogs(
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $facilityId = null
    ): array {
        $conditions = [];
        $params = [];

        if ($startDate) {
            $conditions[] = "created_at >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $conditions[] = "created_at <= ?";
            $params[] = $endDate;
        }

        if ($facilityId) {
            $conditions[] = "facility_id = ?";
            $params[] = $facilityId;
        }

        $where = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : '';
        
        $sql = "SELECT * FROM audit_logs {$where} ORDER BY created_at ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
}
