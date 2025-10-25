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
        require 'login-new.php';
        break;
    case '/auth/login':
        require __DIR__ . $controllers . 'auth.php';
        break;
    case '/dashboard':
        require 'dashboard.php';
        break;
    case '/logout':
        require 'logout.php';
        break;
    default:
        http_response_code(404);
}

?>