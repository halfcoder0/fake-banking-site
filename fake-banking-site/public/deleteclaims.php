<?php
require_once('../controllers/security/session_bootstrap.php');

$userid = $_SESSION['UserID'] ?? null;
$role   = $_SESSION['Role'] ?? null;

if ($userid === '' || $role !== 'USER') {
    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['claim_id'])) {
    $claimId = $_POST['claim_id'];
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
           window.location.href = '/listclaims';
         </script>";
   exit;
}

// Redirect back to claims page
header('Location: /listclaims');
exit;
