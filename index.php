<?php
// C:\servidor-docker\www\index.php
// Enrutador Frontal Definitivo (Front Controller) — FMIS Ganadero Colonias Unidas 2026
declare(strict_types=1);

session_start();

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/Controllers/BovinoController.php';
require_once __DIR__ . '/app/Controllers/InsumoController.php';
require_once __DIR__ . '/app/Controllers/ExtensibleController.php';
require_once __DIR__ . '/app/Controllers/ReporteController.php';

$action = $_GET['action'] ?? 'dashboard';
$authController = new AuthController($pdo);

switch ($action) {
    case 'login':
        $authController->login();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'bovinos':
        (new BovinoController($pdo))->index();
        break;

    case 'bovino_create':
        (new BovinoController($pdo))->create();
        break;

    case 'bovino_show':
        (new BovinoController($pdo))->show();
        break;

    case 'insumos':
        (new InsumoController($pdo))->index();
        break;

    case 'porcinos':
        (new ExtensibleController($pdo))->porcinos();
        break;

    case 'avicolas':
        (new ExtensibleController($pdo))->avicolas();
        break;

    case 'reportes':
        (new ReporteController($pdo))->index();
        break;

    case 'dashboard':
    default:
        (new DashboardController($pdo))->index();
        break;
}