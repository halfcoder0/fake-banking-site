<?php
require_once('../controllers/auth.php');
require_once('../controllers/security/csrf.php');

$auth_controller = new AuthController();
$auth_controller->check_user_role([Roles::STAFF]);

$staffID = $_SESSION["StaffID"] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['claim_id'])) {
    $claimId = trim($_POST['claim_id']) ?? '';

    if (!csrf_verify()){
        error_log("Invalid CSRF");
        return;
    }
    if ($claimId === '' || $staffID === ''){
        error_log("Reject Claims: ClaimID or StaffID is empty");
        return;
    }
    if (!is_valid_uuid($claimId)){
        error_log("Reject Claims: Invalid claimID");
        return;
    }

    $updateQuery = <<<SQL
        UPDATE "Claims"
        SET "ManagedBy" = :uid
        WHERE "ClaimID" = :cid
        AND "ManagedBy" IS NULL; -- only assign if not already taken
    SQL;

    DBController::exec_statement($updateQuery, [
        [':uid', $staffID, PDO::PARAM_STR],
        [':cid', $claimId, PDO::PARAM_STR]
    ]);
}
header('Location: /staff/claims_overview');
exit;
