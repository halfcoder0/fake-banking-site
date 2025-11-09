<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/db_controller.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$request = $_SERVER['REQUEST_URI'];
$controllers = '/../controllers/';

DBController::init_db();

switch ($request) {
    case '':
    case '/':
        require __DIR__ . '/login.php';
        break;
    case '/login':
        require __DIR__ . '/login.php';
        break;
    case '/auth/login':
        require __DIR__ . $controllers . 'auth.php';
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
    case '/otp':
        require __DIR__ . '/verify-otp.php';
        break;
    case '/uploadclaims':
        require __DIR__ . '/uploadclaims.php';
        break;
    case '/listclaims':
        require __DIR__ . '/viewclaims.php';
        break;
    case '/deleteclaims':
        require __DIR__ . '/deleteclaims.php';
        break;
    case '/staffclaimsoverview':
        require __DIR__ . '/staffclaimsoverview.php';
        break;
    case '/assignclaims':
        require __DIR__ . '/assign_claims.php';
        break;
    case '/staffassignedclaims':
        require __DIR__ . '/staffassignedclaims.php';
        break;
    case '/acceptclaims':
        require __DIR__ . '/approve_claim.php';
        break;
    case '/rejectclaims':
        require __DIR__ . '/reject_claim.php';
        break;
    case '/viewCustomers':
        require __DIR__ . '/viewCustomers.php';
        break;
    case '/resetPasswordRequest':
        require __DIR__ . '/resetPasswordRequest.php';
        break;
    default:
        require __DIR__ . '/404.php';
}

?>
