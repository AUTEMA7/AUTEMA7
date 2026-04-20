<?php
/**
 * IlaraNet Bénin - Auth Controller
 * Gestion de l'authentification (login, 2FA, logout)
 */

namespace IlaraNet\Controllers;

use IlaraNet\Core\Arcane;
use IlaraNet\Models\User;
use IlaraNet\Helpers\AuditLogger;
use IlaraNet\Helpers\Security;

class AuthController
{
    private Arcane $arcane;
    private User $userModel;
    private AuditLogger $auditLogger;

    public function __construct()
    {
        $this->arcane = Arcane::getInstance();
        $this->userModel = new User();
        $this->auditLogger = new AuditLogger();
    }

    /**
     * Affiche le formulaire de login
     */
    public function showLogin(): void
    {
        if (is_authenticated()) {
            redirect('/dashboard');
        }

        echo view('auth/login', [
            'title' => 'Connexion - IlaraNet Bénin',
            'csrf_token' => csrf_token()
        ]);
    }

    /**
     * Traite la connexion (étape 1: email + mot de passe)
     */
    public function login(): void
    {
        // Vérifier CSRF
        if (!$this->arcane->verifyCsrf($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token de sécurité invalide');
            redirect('/login');
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($email) || empty($password)) {
            flash('error', 'Email et mot de passe requis');
            redirect('/login');
            return;
        }

        // Authentification
        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            $this->auditLogger->logLogin(0, false, "Échec authentification: {$email}");
            flash('error', 'Email ou mot de passe incorrect');
            redirect('/login');
            return;
        }

        // Stocker temporairement l'user pour l'étape 2FA
        $_SESSION['pending_user'] = $user;
        $_SESSION['pending_time'] = time();

        // Envoyer OTP
        try {
            $otp = $this->userModel->sendOTP($user['id']);
            
            // En dev, afficher l'OTP dans le flash message
            if ($this->arcane->config('app.debug')) {
                flash('info', "OTP (dev): {$otp}");
            } else {
                flash('info', 'Code OTP envoyé par SMS');
            }

            redirect('/login/2fa');
        } catch (\Exception $e) {
            flash('error', 'Erreur envoi OTP: ' . $e->getMessage());
            redirect('/login');
        }
    }

    /**
     * Affiche le formulaire 2FA
     */
    public function show2FA(): void
    {
        if (!isset($_SESSION['pending_user'])) {
            flash('warning', 'Veuillez vous connecter d\'abord');
            redirect('/login');
            return;
        }

        // Vérifier expiration (5 min)
        if (time() - $_SESSION['pending_time'] > 300) {
            unset($_SESSION['pending_user']);
            flash('warning', 'Session expirée, veuillez recommencer');
            redirect('/login');
            return;
        }

        echo view('auth/2fa', [
            'title' => 'Vérification 2FA - IlaraNet Bénin',
            'csrf_token' => csrf_token()
        ]);
    }

    /**
     * Vérifie le code OTP (étape 2: 2FA)
     */
    public function verify2FA(): void
    {
        // Vérifier CSRF
        if (!$this->arcane->verifyCsrf($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token de sécurité invalide');
            redirect('/login');
            return;
        }

        if (!isset($_SESSION['pending_user'])) {
            flash('warning', 'Session expirée');
            redirect('/login');
            return;
        }

        $code = trim($_POST['otp'] ?? '');
        $user = $_SESSION['pending_user'];

        if (empty($code)) {
            flash('error', 'Code OTP requis');
            redirect('/login/2fa');
            return;
        }

        // Vérifier OTP
        if (!$this->userModel->verifyOTP($user['id'], $code)) {
            flash('error', 'Code OTP incorrect ou expiré');
            redirect('/login/2fa');
            return;
        }

        // Connexion réussie - créer la session
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time();
        
        // Nettoyer pending
        unset($_SESSION['pending_user']);
        unset($_SESSION['pending_time']);

        // Log succès
        $this->auditLogger->logLogin($user['id'], true);

        flash('success', "Bienvenue Dr. {$user['prenom']} {$user['nom']}");
        redirect('/dashboard');
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $this->auditLogger->log('LOGOUT', 'user', $userId);
        }

        session_destroy();
        
        flash('info', 'Déconnecté avec succès');
        redirect('/login');
    }

    /**
     * Affiche le formulaire de sélection du service (étape 3)
     */
    public function selectService(): void
    {
        if (!is_authenticated()) {
            redirect('/login');
            return;
        }

        $user = auth_user();
        
        // Récupérer les services où l'utilisateur est assigné
        $services = $this->getServicesForUser($user['id']);

        echo view('auth/select-service', [
            'title' => 'Sélection du service - IlaraNet Bénin',
            'services' => $services,
            'csrf_token' => csrf_token()
        ]);
    }

    /**
     * Vérifie le code service
     */
    public function verifyServiceCode(): void
    {
        if (!is_authenticated()) {
            redirect('/login');
            return;
        }

        // Vérifier CSRF
        if (!$this->arcane->verifyCsrf($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token de sécurité invalide');
            redirect('/services/select');
            return;
        }

        $serviceId = (int) ($_POST['service_id'] ?? 0);
        $serviceCode = trim($_POST['service_code'] ?? '');

        if (!$serviceId || empty($serviceCode)) {
            flash('error', 'Service et code requis');
            redirect('/services/select');
            return;
        }

        // Vérifier que l'utilisateur a accès à ce service
        $services = $this->getServicesForUser(auth_user()['id']);
        $serviceFound = null;
        
        foreach ($services as $s) {
            if ($s['id'] === $serviceId) {
                $serviceFound = $s;
                break;
            }
        }

        if (!$serviceFound) {
            flash('error', 'Accès non autorisé à ce service');
            redirect('/services/select');
            return;
        }

        // Vérifier le code service (hashé en BDD)
        if (!Security::verifyPassword($serviceCode, $serviceFound['code_hash'])) {
            flash('error', 'Code service incorrect');
            redirect('/services/select');
            return;
        }

        // Activer la session pour ce service
        $_SESSION['active_service_id'] = $serviceId;
        $_SESSION['active_service_name'] = $serviceFound['nom'];
        $_SESSION['active_service_code'] = $serviceCode;

        $this->auditLogger->log('SERVICE_ACCESS', 'service', $serviceId, "Accès par " . auth_user()['email']);

        flash('success', "Service {$serviceFound['nom']} activé");
        redirect('/dashboard');
    }

    /**
     * Récupère les services pour un utilisateur
     */
    private function getServicesForUser(int $userId): array
    {
        $db = \IlaraNet\Core\Database::getInstance();
        
        return $db->fetchAll(
            "SELECT s.*, ss.assigned_at
             FROM services s
             INNER JOIN service_staff ss ON s.id = ss.service_id
             WHERE ss.user_id = ? AND s.deleted_at IS NULL
             ORDER BY s.nom",
            [$userId]
        );
    }

    /**
     * Réinitialisation de mot de passe (demande)
     */
    public function forgotPassword(): void
    {
        echo view('auth/forgot-password', [
            'title' => 'Réinitialisation mot de passe - IlaraNet Bénin',
            'csrf_token' => csrf_token()
        ]);
    }

    /**
     * Traite la demande de réinitialisation
     */
    public function sendResetLink(): void
    {
        // TODO: Implémenter l'envoi de lien de réinitialisation par email/SMS
        flash('info', 'Un lien de réinitialisation vous sera envoyé si votre email est enregistré');
        redirect('/login');
    }
}
