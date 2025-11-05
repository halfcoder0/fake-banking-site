<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/db_controller.php';
require_once __DIR__ .'/../controllers/helpers.php';
require_once __DIR__ .'/../controllers/enum.php';
require("../controllers/security/session_bootstrap.php");
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$controllers = '/../controllers/';

DBController::init_db();

if ($request !== '/' && str_ends_with($request, '.php')) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

switch ($request) {
    case '':
    case '/':
        require __DIR__ . '/login.php';
        break;
    case '/login':
        require __DIR__ . '/login.php';
        break;
    case '/dashboard':
        require __DIR__ . '/dashboard.php';
        break;
    case '/logout':
        require __DIR__ . '/logout.php';
        break;
    case '/register':
        require __DIR__ . '/register.php';
        break;
    case '/db':
        require __DIR__ . '/../includes/dbconnection.php';
        break;
    case '/transfer':
        require __DIR__ . '/transfer.php';
        break;
    case '/staff-dashboard':
        require __DIR__ . '/staff_dashboard.php';
        break;
    case '/admin-dashboard':
        require __DIR__ . '/admin_dashboard.php';
        break;
    case '/transactions':
        require __DIR__ . $controllers . '/api/transactions.php';
        break;
    case '/accounts':
        require __DIR__ . $controllers . '/api/accounts.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/404.php';
        exit;
}

?>