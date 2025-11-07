<?php
require __DIR__ . '/../vendor/autoload.php'; 

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); 
$dotenv->load();


$request = $_SERVER['REQUEST_URI'];
$controllers = '/../controllers/';

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
    case '/create_customer_account':
        require __DIR__ . '/create_customer_account.php';
        break;
    case '/delete_customer_account':
        require __DIR__ . '/delete_customer_account.php';
        break;
    case '/customer_account':
        require __DIR__ . $controllers . 'customer_account.php';
        break;
    case '/register':
        require __DIR__ . '/register.php';
        break;
    case '/db':
        require __DIR__ . '/../includes/dbconnection.php';
        break;
    default:
        require __DIR__ . '/404.php';
}

?>