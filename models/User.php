<?php
/**
 * IlaraNet Bénin - User Model
 */

namespace IlaraNet\Models;

use IlaraNet\Core\Database;
use IlaraNet\Helpers\Security;
use IlaraNet\Helpers\AuditLogger;

class User
{
    private Database $db;
    private AuditLogger $auditLogger;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->auditLogger = new AuditLogger();
    }

    /**
     * Trouve un utilisateur par email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL",
            ['email' => $email]
        );
    }

    /**
     * Trouve un utilisateur par ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    /**
     * Trouve un utilisateur par numéro d'ordre professionnel
     */
    public function findByOrderNumber(string $orderNumber): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE numero_ordre = ? AND deleted_at IS NULL",
            [$orderNumber]
        );
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function create(array $data): int
    {
        // Hash du mot de passe
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        
        $userId = $this->db->insert('users', $data);
        
        $this->auditLogger->log('USER_CREATED', 'user', $userId, "Création utilisateur: {$data['email']}");
        
        return $userId;
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(int $id, array $data): bool
    {
        // Si mot de passe inclus, le hasher
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $affected = $this->db->update('users', $data, 'id = ?', [$id]);
        
        $this->auditLogger->log('USER_UPDATED', 'user', $id, "Mise à jour utilisateur ID: {$id}");
        
        return $affected > 0;
    }

    /**
     * Supprime un utilisateur (soft delete)
     */
    public function delete(int $id): bool
    {
        $affected = $this->db->update(
            'users',
            ['deleted_at' => date('Y-m-d H:i:s')],
            'id = ?',
            [$id]
        );
        
        $this->auditLogger->log('USER_DELETED', 'user', $id);
        
        return $affected > 0;
    }

    /**
     * Vérifie les identifiants de connexion
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return null;
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            return null;
        }

        if ($user['status'] !== 'active') {
            return null;
        }

        // Ne pas retourner le mot de passe hashé
        unset($user['password']);
        
        return $user;
    }

    /**
     * Envoie un OTP pour 2FA
     */
    public function sendOTP(int $userId): string
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }

        $otp = Security::generatePin(6);
        
        // Sauvegarder l'OTP
        $this->db->insert('otps', [
            'user_id' => $userId,
            'code' => Security::hashPassword($otp),
            'type' => '2FA',
            'expires_at' => date('Y-m-d H:i:s', time() + 300), // 5 minutes
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // TODO: Envoyer SMS via API (MTN, Moov, Celtiis)
        // Pour l'instant, on retourne l'OTP pour le dev
        error_log("OTP pour {$user['email']}: {$otp}");
        
        return $otp;
    }

    /**
     * Vérifie l'OTP
     */
    public function verifyOTP(int $userId, string $code): bool
    {
        $otp = $this->db->fetch(
            "SELECT * FROM otps 
             WHERE user_id = ? 
             AND type = '2FA'
             AND expires_at > NOW()
             ORDER BY created_at DESC 
             LIMIT 1",
            [$userId]
        );

        if (!$otp) {
            return false;
        }

        if (!Security::verifyPassword($code, $otp['code'])) {
            return false;
        }

        // Invalider l'OTP après usage
        $this->db->update('otps', ['used' => 1], 'id = ?', [$otp['id']]);
        
        return true;
    }

    /**
     * Récupère les utilisateurs d'un établissement
     */
    public function getByFacility(int $facilityId, ?string $role = null): array
    {
        $sql = "SELECT * FROM users WHERE facility_id = ? AND deleted_at IS NULL";
        $params = [$facilityId];

        if ($role) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }

        $sql .= " ORDER BY nom, prenom";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Récupère les utilisateurs d'un service
     */
    public function getByService(int $serviceId): array
    {
        return $this->db->fetchAll(
            "SELECT u.* FROM users u
             INNER JOIN service_staff ss ON u.id = ss.user_id
             WHERE ss.service_id = ? AND u.deleted_at IS NULL
             ORDER BY u.nom, u.prenom",
            [$serviceId]
        );
    }

    /**
     * Assigne un utilisateur à un service
     */
    public function assignToService(int $userId, int $serviceId): bool
    {
        try {
            $this->db->insert('service_staff', [
                'user_id' => $userId,
                'service_id' => $serviceId,
                'assigned_at' => date('Y-m-d H:i:s')
            ]);
            
            $this->auditLogger->log('SERVICE_ASSIGNMENT', 'user', $userId, "Assigné au service ID: {$serviceId}");
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retire un utilisateur d'un service
     */
    public function removeFromService(int $userId, int $serviceId): bool
    {
        $affected = $this->db->delete(
            'service_staff',
            'user_id = ? AND service_id = ?',
            [$userId, $serviceId]
        );
        
        $this->auditLogger->log('SERVICE_REMOVAL', 'user', $userId, "Retiré du service ID: {$serviceId}");
        
        return $affected > 0;
    }

    /**
     * Change le rôle d'un utilisateur
     */
    public function changeRole(int $userId, string $newRole): bool
    {
        $allowedRoles = ['super_admin', 'directeur', 'chef_service', 'medecin', 'medecin_chef', 
                         'infirmier', 'sage_femme', 'laborantin', 'radiologue', 'comptable', 
                         'pharmacien', 'accueil', 'admin'];
        
        if (!in_array($newRole, $allowedRoles)) {
            throw new \Exception("Rôle non valide");
        }

        $result = $this->update($userId, ['role' => $newRole]);
        
        $this->auditLogger->logSensitiveChange('user', $userId, 'role', 'ancien', $newRole);
        
        return $result;
    }

    /**
     * Réinitialise le mot de passe
     */
    public function resetPassword(int $userId, string $newPassword): bool
    {
        $result = $this->update($userId, ['password' => $newPassword]);
        
        $this->auditLogger->log('PASSWORD_RESET', 'user', $userId);
        
        return $result;
    }

    /**
     * Active ou désactive un utilisateur
     */
    public function toggleStatus(int $userId): bool
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            return false;
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $result = $this->update($userId, ['status' => $newStatus]);
        
        $this->auditLogger->log('USER_STATUS_CHANGE', 'user', $userId, "Statut changé: {$newStatus}");
        
        return $result;
    }

    /**
     * Récupère les statistiques des utilisateurs
     */
    public function getStats(int $facilityId): array
    {
        return $this->db->fetch(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN role = 'medecin' OR role = 'medecin_chef' THEN 1 ELSE 0 END) as medecins,
                SUM(CASE WHEN role = 'infirmier' THEN 1 ELSE 0 END) as infirmiers,
                SUM(CASE WHEN role = 'accueil' THEN 1 ELSE 0 END) as accueil,
                SUM(CASE WHEN role = 'comptable' THEN 1 ELSE 0 END) as comptables
             FROM users 
             WHERE facility_id = ? AND deleted_at IS NULL",
            [$facilityId]
        );
    }
}
