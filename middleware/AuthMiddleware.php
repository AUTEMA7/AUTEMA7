<?php
/**
 * IlaraNet Bénin - Middleware d'Authentification
 */

namespace IlaraNet\Middleware;

use IlaraNet\Core\Arcane;
use IlaraNet\Helpers\Security;
use IlaraNet\Helpers\AuditLogger;

class AuthMiddleware
{
    private Arcane $arcane;
    private AuditLogger $auditLogger;

    public function __construct()
    {
        $this->arcane = Arcane::getInstance();
        $this->auditLogger = new AuditLogger();
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    public function handle(): bool
    {
        if (!isset($_SESSION['user'])) {
            $this->auditLogger->logUnauthorizedAccess(
                $_SERVER['REQUEST_URI'],
                'Non authentifié'
            );
            
            if ($this->isApiRequest()) {
                json_response(['error' => 'Non authentifié'], 401);
            }
            
            flash('error', 'Veuillez vous connecter');
            redirect('/login');
            return false;
        }

        // Vérifier expiration session
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > $this->arcane->config('security.session_timeout'))) {
            session_destroy();
            flash('warning', 'Session expirée, veuillez vous reconnecter');
            redirect('/login');
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Vérifie le rôle de l'utilisateur
     */
    public function checkRole(array $allowedRoles): bool
    {
        if (!$this->handle()) {
            return false;
        }

        $user = $_SESSION['user'];
        
        if (!in_array($user['role'], $allowedRoles)) {
            $this->auditLogger->logUnauthorizedAccess(
                $_SERVER['REQUEST_URI'],
                "Rôle non autorisé: {$user['role']}"
            );

            if ($this->isApiRequest()) {
                json_response(['error' => 'Accès non autorisé'], 403);
            }

            flash('error', 'Vous n\'avez pas les permissions nécessaires');
            redirect('/dashboard');
            return false;
        }

        return true;
    }

    /**
     * Vérifie que l'utilisateur appartient à l'établissement
     */
    public function checkFacility(int $facilityId): bool
    {
        if (!$this->handle()) {
            return false;
        }

        $user = $_SESSION['user'];
        
        // Super Admin peut tout voir
        if ($user['role'] === 'super_admin') {
            return true;
        }

        if ($user['facility_id'] !== $facilityId) {
            $this->auditLogger->logUnauthorizedAccess(
                $_SERVER['REQUEST_URI'],
                "Accès établissement étranger: {$facilityId}"
            );

            flash('error', 'Accès refusé à cet établissement');
            redirect('/dashboard');
            return false;
        }

        return true;
    }

    /**
     * Vérifie le code service (pour accès au service spécifique)
     */
    public function checkServiceCode(string $serviceCode): bool
    {
        if (!$this->handle()) {
            return false;
        }

        if (!Security::isValidServiceCode($serviceCode)) {
            flash('error', 'Code service invalide');
            redirect('/dashboard');
            return false;
        }

        // Vérifier dans la session si le code service est valide
        if (!isset($_SESSION['active_service_code']) || 
            $_SESSION['active_service_code'] !== $serviceCode) {
            
            flash('error', 'Code service incorrect ou expiré');
            redirect('/services/select');
            return false;
        }

        return true;
    }

    /**
     * Vérifie le code patient (pour accès dossier médical)
     */
    public function checkPatientCode(string $patientCode, int $patientFileId): bool
    {
        if (!$this->handle()) {
            return false;
        }

        if (!Security::isValidPatientCode($patientCode)) {
            return false;
        }

        // Vérifier dans la session si le code patient est valide pour ce dossier
        if (!isset($_SESSION['patient_access'][$patientFileId]) || 
            $_SESSION['patient_access'][$patientFileId]['code'] !== $patientCode ||
            $_SESSION['patient_access'][$patientFileId]['expires'] < time()) {
            
            return false;
        }

        return true;
    }

    /**
     * Vérifie si c'est une requête API
     */
    private function isApiRequest(): bool
    {
        return strpos($_SERVER['REQUEST_URI'], '/api/') === 0 ||
               isset($_SERVER['HTTP_ACCEPT']) && 
               strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    }

    /**
     * Middleware pour Directeur uniquement
     */
    public function directorOnly(): bool
    {
        return $this->checkRole(['directeur']);
    }

    /**
     * Middleware pour Chef de Service uniquement
     */
    public function chefServiceOnly(): bool
    {
        return $this->checkRole(['chef_service']);
    }

    /**
     * Middleware pour Médecins uniquement
     */
    public function medecinOnly(): bool
    {
        return $this->checkRole(['medecin', 'medecin_chef']);
    }

    /**
     * Middleware pour personnel soignant
     */
    public function soignantOnly(): bool
    {
        return $this->checkRole(['medecin', 'infirmier', 'sage_femme', 'laborantin', 'radiologue']);
    }

    /**
     * Middleware pour Super Admin uniquement
     */
    public function superAdminOnly(): bool
    {
        return $this->checkRole(['super_admin']);
    }
}

// Helper function
if (!function_exists('auth_middleware')) {
    function auth_middleware(): AuthMiddleware
    {
        return new AuthMiddleware();
    }
}
