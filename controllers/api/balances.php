<?php
require_once __DIR__ . '/../BalanceController.php';
try {
    // Enforce login
    if (empty($_SESSION['UserID']) || empty($_SESSION['CustomerID']) || $_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["GetAccounts"])) {
        redirect_404();
    }

    DBController::init_db();
    $ctrl = new BalanceController();
    $rows = $ctrl->listAccounts($_SESSION['CustomerID']);

    header('Content-Type: application/json');
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Exception $exception) {
    error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
    redirect_500();
} catch (Throwable $throwable) {
    error_log($throwable->getMessage() . "\n" . $throwable->getTraceAsString());
    redirect_500();
}
