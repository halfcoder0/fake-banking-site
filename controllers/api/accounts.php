<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../AccountController.php';
try {
    // Enforce login
    if (empty($_SESSION['UserID']) || empty($_SESSION['CustomerID']) || $_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["GetAccounts"])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    DBController::init_db();
    $ctrl = new AccountController();
    $rows = $ctrl->listAccounts($_SESSION['CustomerID']); 

    $rows = filter_var_array($rows, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
    error_log($e->getMessage());
    error_log($e->getTraceAsString());
}
