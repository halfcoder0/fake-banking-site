<?php
require_once('../controllers/auth.php');
require_once('../controllers/security/csrf.php');

$auth_controller = new AuthController();
$auth_controller->check_user_role([Roles::USER]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['claim_id'])) {
    $claimId = trim($_POST['claim_id']) ?? '';
    $userid = $_SESSION['UserID'] ?? '';

    if (!csrf_verify()){
        error_log("Invalid CSRF");
        return;
    }
    if ($claimId === '' || $userid === ''){
        error_log("Reject Claims: ClaimID or StaffID is empty");
        return;
    }
    if (!is_valid_uuid($claimId)){
        error_log("Reject Claims: Invalid claimID");
        return;
    }

    // Delete only if the claim belongs to this user
    $deleteQuery = <<<SQL
        DELETE FROM "Claims"
        WHERE "ClaimID" = :cid
        AND "CustomerID" = (
            SELECT "CustomerID" FROM "Customer" WHERE "UserID" = :uid
        );
    SQL;

    DBController::exec_statement($deleteQuery, [
        [':cid', $claimId, PDO::PARAM_STR],
        [':uid', $userid, PDO::PARAM_STR]
    ]);

}else {
  echo "<script>
           alert('Error, please try again');
           window.location.href = '/view_claims';
         </script>";
   exit;
}

// Redirect back to claims page
header('Location: /view_claims');
exit;
