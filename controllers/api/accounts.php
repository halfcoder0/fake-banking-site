<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db_controller.php';
require_once __DIR__ . '/../AccountController.php';
require_once __DIR__ . '/../security/session_bootstrap.php';

session_start();

try {
    // Enforce login
    if (empty($_SESSION['UserID']) || empty($_SESSION['CustomerID'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    DBController::init_db();
    $ctrl = new AccountController();
    $rows = $ctrl->listAccounts($_SESSION['CustomerID']);  // scope to me
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
