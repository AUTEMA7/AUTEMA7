<?php

declare(strict_types=1);

use App\Arcane;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\EmergencyController;
use App\Controller\HospitalizationController;
use App\Controller\IntegrationController;
use App\Controller\LabController;
use App\Controller\PatientController;
use App\Controller\PharmacyController;
use App\Support\Env;

session_start();

require_once __DIR__ . '/../src/Support/Env.php';
Env::load(__DIR__ . '/../.env');

require_once __DIR__ . '/../src/Arcane.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Support/Auth.php';
require_once __DIR__ . '/../src/Support/AuditLogger.php';
require_once __DIR__ . '/../src/Support/SmsGateway.php';
require_once __DIR__ . '/../src/Support/OtpService.php';
require_once __DIR__ . '/../src/Controller/AuthController.php';
require_once __DIR__ . '/../src/Controller/DashboardController.php';
require_once __DIR__ . '/../src/Controller/PatientController.php';
require_once __DIR__ . '/../src/Controller/EmergencyController.php';
require_once __DIR__ . '/../src/Controller/HospitalizationController.php';
require_once __DIR__ . '/../src/Controller/LabController.php';
require_once __DIR__ . '/../src/Controller/PharmacyController.php';
require_once __DIR__ . '/../src/Controller/IntegrationController.php';

$app = new Arcane();

$app->get('/', [AuthController::class, 'showLogin']);
$app->post('/login', [AuthController::class, 'login']);
$app->get('/otp', [AuthController::class, 'showOtp']);
$app->post('/otp', [AuthController::class, 'verifyOtp']);
$app->get('/service-access', [AuthController::class, 'showServiceAccess']);
$app->post('/service-access', [AuthController::class, 'verifyServiceAccess']);
$app->post('/logout', [AuthController::class, 'logout']);

$app->get('/dashboard', [DashboardController::class, 'index']);
$app->get('/patients', [PatientController::class, 'index']);
$app->post('/patients', [PatientController::class, 'store']);
$app->post('/integrations/anip/verify', [IntegrationController::class, 'verifyAnip']);
$app->post('/payments/mobile', [IntegrationController::class, 'mobilePayment']);

$app->get('/modules/emergency', [EmergencyController::class, 'index']);
$app->post('/modules/emergency', [EmergencyController::class, 'store']);
$app->get('/modules/hospitalization', [HospitalizationController::class, 'index']);
$app->post('/modules/hospitalization', [HospitalizationController::class, 'store']);
$app->get('/modules/lab', [LabController::class, 'index']);
$app->post('/modules/lab', [LabController::class, 'store']);
$app->get('/modules/pharmacy', [PharmacyController::class, 'index']);
$app->post('/modules/pharmacy', [PharmacyController::class, 'sendOrder']);

$app->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
