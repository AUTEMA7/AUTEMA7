<?php
/**
 * IlaraNet Bénin - Entry Point (Front Controller)
 */

// Démarrage session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    session_start();
}

// Autoloader PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'IlaraNet\\';
    $baseDir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Charger les helpers
require_once __DIR__ . '/helpers/Security.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

// Charger le core Arcane
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Arcane.php';

// Initialiser l'application
$app = \IlaraNet\Core\Arcane::getInstance();

// Enregistrer les middlewares
$app->middleware('auth', function() {
    return auth_middleware()->handle();
});

$app->middleware('admin', function() {
    return auth_middleware()->checkRole(['super_admin', 'admin']);
});

// Routes publiques
$app->get('/', function() {
    if (is_authenticated()) {
        redirect('/dashboard');
    }
    echo view('landing', [
        'title' => 'IlaraNet Bénin - Plateforme Numérique de Gestion des Centres de Santé',
        'meta_description' => 'Écosystème numérique complet pour hôpitaux, cliniques et pharmacies au Bénin. Conformité ANIP & OHADA.'
    ]);
});

$app->get('/login', [\IlaraNet\Controllers\AuthController::class, 'showLogin']);
$app->post('/login', [\IlaraNet\Controllers\AuthController::class, 'login']);
$app->get('/login/2fa', [\IlaraNet\Controllers\AuthController::class, 'show2FA']);
$app->post('/login/2fa', [\IlaraNet\Controllers\AuthController::class, 'verify2FA']);
$app->get('/logout', [\IlaraNet\Controllers\AuthController::class, 'logout']);
$app->get('/register', function() {
    if (is_authenticated()) {
        redirect('/dashboard');
    }
    echo view('auth/register', ['title' => 'Inscription - IlaraNet Bénin']);
});
$app->get('/forgot-password', [\IlaraNet\Controllers\AuthController::class, 'forgotPassword']);
$app->post('/forgot-password', [\IlaraNet\Controllers\AuthController::class, 'sendResetLink']);

// Routes protégées (après authentification)
$app->get('/services/select', [\IlaraNet\Controllers\AuthController::class, 'selectService']);
$app->post('/services/select', [\IlaraNet\Controllers\AuthController::class, 'verifyServiceCode']);

// Dashboard
$app->get('/dashboard', function() {
    if (!is_authenticated()) {
        redirect('/login');
    }
    
    $user = auth_user();
    $db = \IlaraNet\Core\Database::getInstance();
    
    // Récupérer les stats selon le rôle
    $stats = null;
    if ($user['role'] === 'directeur') {
        // Stats globales pour le directeur
        $stats = [
            'patients_today' => $db->fetch("SELECT COUNT(*) as count FROM patient_files WHERE facility_id = ? AND DATE(created_at) = CURDATE()", [$user['facility_id']])['count'],
            'appointments_today' => $db->fetch("SELECT COUNT(*) as count FROM appointments WHERE facility_id = ? AND DATE(date) = CURDATE()", [$user['facility_id']])['count'],
            'revenue_today' => $db->fetch("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE facility_id = ? AND DATE(created_at) = CURDATE()", [$user['facility_id']])['total'],
            'beds_occupied' => $db->fetch("SELECT COUNT(*) as count FROM beds WHERE facility_id = ? AND status = 'occupé'", [$user['facility_id']])['count'],
        ];
    }
    
    echo view('dashboard/index', [
        'title' => 'Tableau de bord - IlaraNet Bénin',
        'user' => $user,
        'stats' => $stats
    ], 'dashboard');
});

// Exécuter le routeur
$app->run();
