<?php

declare(strict_types=1);

use App\Arcane;
use App\Controller\AuthController;
use App\Controller\PatientController;
use App\Support\Env;

session_start();

require_once __DIR__ . '/../src/Support/Env.php';
Env::load(__DIR__ . '/../.env');

require_once __DIR__ . '/../src/Arcane.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Controller/AuthController.php';
require_once __DIR__ . '/../src/Controller/PatientController.php';

$app = new Arcane();

$app->get('/', [AuthController::class, 'showLogin']);
$app->post('/login', [AuthController::class, 'login']);
$app->post('/logout', [AuthController::class, 'logout']);
$app->get('/dashboard', [PatientController::class, 'index']);
$app->post('/patients', [PatientController::class, 'store']);

$app->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
