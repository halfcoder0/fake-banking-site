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
    case '/create_staff':
        require __DIR__ . '/create_staff.php';
        break;
    case '/update_staff':
        require __DIR__ . '/update_staff.php';
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
    case '/balances':
        require __DIR__ . $controllers . '/api/balances.php';
        break;
    case '/profile':
        require __DIR__ . '/profile.php';
    case '/admin_controller':
        require __DIR__ . '/../controllers/admin_controller.php';
        break;
    case '/accounts':
        require __DIR__ . $controllers . '/api/accounts.php';
        break;
    case '/create_delete_user_account':
        require __DIR__ . '/create_delete_user_account.php';
        break;
    case '/approve_claim':
        require __DIR__ . '/approve_claim.php';
        break;
    case '/assign_claims':
        require __DIR__ . '/assign_claims.php';
        break;
    case '/delete_claims':
        require __DIR__ . '/deleteclaims.php';
        break;
    case '/reject_claim':
        require __DIR__ . '/reject_claim.php';
        break;
    case '/staff/assigned_claims':
        require __DIR__ . '/staffassignedclaims.php';
        break;
    case '/staff/claims_overview':
        require __DIR__ . '/staffclaimsoverview.php';
        break;
    case '/upload_claims':
        require __DIR__ . '/uploadclaims.php';
        break;
    case '/view_claims':
        require __DIR__ . '/viewclaims.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/404.php';
        exit;
}

?>